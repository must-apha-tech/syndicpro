import React from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import LoginForm from '../components/Auth/LoginForm';
import { useAuth } from '../context/AuthContext';
import { useEffect } from 'react';

const LoginPage = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const successMessage = location.state?.message;

  useEffect(() => {
    if (user) {
      navigate('/dashboard');
    }
  }, [user, navigate]);

  return (
    <div>
      {successMessage && (
        <div style={{ 
          backgroundColor: '#dcfce7', 
          color: '#166534', 
          padding: '1rem', 
          textAlign: 'center',
          maxWidth: '400px',
          margin: '1rem auto',
          borderRadius: '8px'
        }}>
          {successMessage}
        </div>
      )}
      <LoginForm onRegisterClick={() => navigate('/register')} />
    </div>
  );
};

export default LoginPage;
