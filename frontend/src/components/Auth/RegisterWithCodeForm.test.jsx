import React from 'react';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { BrowserRouter } from 'react-router-dom';
import axios from 'axios';
import RegisterWithCodeForm from './RegisterWithCodeForm';

jest.mock('axios');

describe('RegisterWithCodeForm', () => {
  test('renders step 1 by default', () => {
    render(
      <BrowserRouter>
        <RegisterWithCodeForm />
      </BrowserRouter>
    );
    
    expect(screen.getByText('Vérification du code')).toBeInTheDocument();
    expect(screen.getByPlaceholderText('Ex: ABC123')).toBeInTheDocument();
  });

  test('validates code and moves to step 2', async () => {
    axios.post.mockResolvedValueOnce({
      data: {
        residence: { id: 1, name: 'Residence A', address: 'Add A', city: 'City A' },
        lot: { id: 10, numero: '101', type: 'Appart', surface: 50, quote_part: 100 }
      }
    });

    render(
      <BrowserRouter>
        <RegisterWithCodeForm />
      </BrowserRouter>
    );

    fireEvent.change(screen.getByPlaceholderText('Ex: ABC123'), { target: { value: 'ABC123' } });
    fireEvent.click(screen.getByText('Valider le code'));

    await waitFor(() => {
      expect(screen.getByText('Confirmation du lot')).toBeInTheDocument();
      expect(screen.getByText('Residence A')).toBeInTheDocument();
      expect(screen.getByText('Lot n°: 101')).toBeInTheDocument();
    });
  });
});
