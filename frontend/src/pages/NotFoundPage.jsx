import React from 'react';
import { Link } from 'react-router-dom';

const NotFoundPage = () => {
  return (
    <div style={{ 
      display: 'flex', 
      flexDirection: 'column', 
      alignItems: 'center', 
      justifyContent: 'center', 
      height: '100vh',
      textAlign: 'center',
      padding: '2rem'
    }}>
      <h1 style={{ fontSize: '6rem', margin: 0, color: 'var(--primary-color)' }}>404</h1>
      <h2>Oups! Page non trouvée</h2>
      <p style={{ color: 'var(--text-secondary)', marginBottom: '2rem' }}>
        La page que vous recherchez n'existe pas ou a été déplacée.
      </p>
      <Link to="/" style={{ 
        backgroundColor: 'var(--primary-color)', 
        color: 'white', 
        padding: '0.75rem 1.5rem', 
        borderRadius: 'var(--radius)',
        fontWeight: '600'
      }}>
        Retour au tableau de bord
      </Link>
    </div>
  );
};

export default NotFoundPage;
