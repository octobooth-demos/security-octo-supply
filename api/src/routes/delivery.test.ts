import express from 'express';
import request from 'supertest';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import deliveryRouter from './delivery';
import { errorHandler } from '../utils/errors';

const { mockExecFile, mockGetDeliveriesRepository, mockRepository } = vi.hoisted(() => ({
  mockExecFile: vi.fn(),
  mockGetDeliveriesRepository: vi.fn(),
  mockRepository: {
    findById: vi.fn(),
    updateStatus: vi.fn(),
  },
}));

vi.mock('child_process', () => ({
  execFile: mockExecFile,
}));

vi.mock('../repositories/deliveriesRepo', () => ({
  getDeliveriesRepository: mockGetDeliveriesRepository,
}));

const delivery = {
  deliveryId: 1,
  supplierId: 1,
  deliveryDate: '2026-10-20',
  name: 'Test delivery',
  description: 'Test',
  status: 'pending',
};

describe('Delivery status API', () => {
  let app: express.Express;

  beforeEach(() => {
    vi.clearAllMocks();
    mockGetDeliveriesRepository.mockResolvedValue(mockRepository);
    mockRepository.findById.mockResolvedValue(delivery);
    mockRepository.updateStatus.mockResolvedValue({ ...delivery, status: 'in-transit' });
    mockExecFile.mockImplementation((...args: unknown[]) => {
      const callback = args[3] as (error: Error | null, stdout: string, stderr: string) => void;
      callback(null, 'notified', '');
    });

    app = express();
    app.use(express.json());
    app.use('/deliveries', deliveryRouter);
    app.use(errorHandler);
  });

  it('passes a delivery partner as a single argument without invoking a shell', async () => {
    const deliveryPartner = 'partner; echo compromised';
    const response = await request(app)
      .put('/deliveries/1/status')
      .send({ status: 'in-transit', deliveryPartner });

    expect(response.status).toBe(200);
    expect(response.body.commandOutput).toBe('notified');
    expect(mockExecFile).toHaveBeenCalledWith(
      'notify',
      [deliveryPartner],
      { shell: false },
      expect.any(Function),
    );
  });

  it('rejects a non-string delivery partner', async () => {
    const response = await request(app)
      .put('/deliveries/1/status')
      .send({ status: 'in-transit', deliveryPartner: ['partner'] });

    expect(response.status).toBe(400);
    expect(mockExecFile).not.toHaveBeenCalled();
    expect(mockRepository.updateStatus).not.toHaveBeenCalled();
  });
});
