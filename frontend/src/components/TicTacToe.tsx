import { useMemo, useState } from 'react';
import { useTheme } from '../context/ThemeContext';

type Player = 'X' | 'O';
type CellValue = Player | null;

const winningLines = [
  [0, 1, 2],
  [3, 4, 5],
  [6, 7, 8],
  [0, 3, 6],
  [1, 4, 7],
  [2, 5, 8],
  [0, 4, 8],
  [2, 4, 6],
];

function getWinner(board: CellValue[]): Player | null {
  for (const [a, b, c] of winningLines) {
    if (board[a] && board[a] === board[b] && board[b] === board[c]) {
      return board[a];
    }
  }
  return null;
}

export default function TicTacToe() {
  const { darkMode } = useTheme();
  const [board, setBoard] = useState<CellValue[]>(Array(9).fill(null));
  const [nextPlayer, setNextPlayer] = useState<Player>('X');

  const winner = useMemo(() => getWinner(board), [board]);
  const isDraw = useMemo(() => board.every((cell) => cell !== null) && !winner, [board, winner]);

  const status = winner
    ? `Player ${winner} wins!`
    : isDraw
      ? 'Draw game.'
      : `Player ${nextPlayer}'s turn`;

  const handleCellClick = (index: number) => {
    if (board[index] || winner) {
      return;
    }

    const updatedBoard = [...board];
    updatedBoard[index] = nextPlayer;
    setBoard(updatedBoard);
    setNextPlayer(nextPlayer === 'X' ? 'O' : 'X');
  };

  const resetGame = () => {
    setBoard(Array(9).fill(null));
    setNextPlayer('X');
  };

  return (
    <div
      className={`min-h-[calc(100vh-4rem)] pt-24 pb-10 px-4 ${darkMode ? 'bg-dark text-light' : 'bg-gray-100 text-gray-800'} transition-colors duration-300`}
    >
      <div
        className={`max-w-md mx-auto rounded-xl p-6 shadow-lg ${darkMode ? 'bg-gray-800' : 'bg-white'} transition-colors duration-300`}
      >
        <h1 className="text-3xl font-bold text-center mb-2">Tic-Tac-Toe</h1>
        <p className={`text-center mb-6 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{status}</p>

        <div className="grid grid-cols-3 gap-3" role="grid" aria-label="Tic-Tac-Toe board">
          {board.map((cell, index) => (
            <button
              key={index}
              type="button"
              onClick={() => handleCellClick(index)}
              className={`h-24 rounded-md border text-3xl font-bold focus:outline-none focus:ring-2 focus:ring-primary ${
                darkMode
                  ? 'border-gray-600 bg-gray-700 hover:bg-gray-600'
                  : 'border-gray-300 bg-gray-50 hover:bg-gray-100'
              } transition-colors`}
              aria-label={`Cell ${index + 1}${cell ? `, ${cell}` : ''}`}
            >
              {cell}
            </button>
          ))}
        </div>

        <button
          type="button"
          onClick={resetGame}
          className="mt-6 w-full bg-primary hover:bg-accent text-white py-2 px-4 rounded-md font-medium transition-colors"
        >
          Restart Game
        </button>
      </div>
    </div>
  );
}
