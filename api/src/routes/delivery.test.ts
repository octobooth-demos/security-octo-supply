import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import express from 'express';
import request from 'supertest';
import { execFile } from 'child_process';
import { closeDatabase } from '../db/sqlite';
import { Delivery } from '../models/delivery';
import { DeliveriesRepository } from '../repositories/deliveriesRepo';
import deliveryRouter from './delivery';

const execFileMock = vi.hoisted(() => vi.fn());

vi.mock('child_process', () => ({
  execFile: execFileMock,
}));

const delivery: Delivery = {
  deliveryId: 1,
  supplierId: 1,
  deliveryDate: '2025-01-01',
  name: 'Test delivery',
  description: 'Test delivery description',
  status: 'pending',
};

describe('Delivery API status updates', () => {
  let app: express.Express;

  beforeEach(async () => {
    await closeDatabase();
    vi.stubEnv('NODE_ENV', 'test');
    vi.spyOn(DeliveriesRepository.prototype, 'findById').mockResolvedValue(delivery);
    vi.spyOn(DeliveriesRepository.prototype, 'updateStatus').mockResolvedValue({
      ...delivery,
      status: 'in-transit',
    });
    execFileMock.mockImplementation(
      (_command: string, _args: string[], callback: (error: Error | null, stdout: string) => void) =>
        callback(null, 'notified'),
    );

    app = express();
    app.use(express.json());
    app.use('/deliveries', deliveryRouter);
  });

  afterEach(async () => {
    vi.restoreAllMocks();
    vi.unstubAllEnvs();
    await closeDatabase();
  });

  it('passes the delivery partner as a single argument without shell interpretation', async () => {
    const deliveryPartner = 'partner; echo injected';
    const response = await request(app)
      .put('/deliveries/1/status')
      .send({ status: 'in-transit', deliveryPartner });

    expect(response.status).toBe(200);
    expect(response.body.commandOutput).toBe('notified');
    expect(execFile).toHaveBeenCalledWith('notify', [deliveryPartner], expect.any(Function));
  });
});
