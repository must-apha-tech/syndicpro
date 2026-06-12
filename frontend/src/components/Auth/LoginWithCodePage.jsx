import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import axios from 'axios';
import styles from './Auth.module.css';

const LoginWithCodePage = () => {
  const navigate = useNavigate();
  const [code, setCode] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (code.length !== 6) {
      setError('Le code doit comporter 6 caractères');
      return;
    }

    setLoading(true);
    setError('');

    try {
      // For quick access, we first validate the code
      const response = await axios.post('/api/auth/validate-code', { code });
      
      // If valid, we redirect to registration with the code pre-filled
      // or if the user is already registered (link check needed in real app)
      // For now, based on requirements, this is a path to registration
      navigate(`/register?code=${code}`);
    } catch (err) {
      setError(err.response?.data?.error || 'Code invalide ou expiré');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className={styles.authContainer}>
      <div className={styles.authCard}>
        <div className={styles.authHeader}>
          <h1>Accès Résident</h1>
          <p>Utilisez votre code d'accès pour vous enregistrer</p>
        </div>

        {error && <div className={styles.errorAlert}>{error}</div>}

        <form onSubmit={handleSubmit} className={styles.authForm}>
          <div className={styles.inputBox}>
            <input
              type="text"
              value={code}
              onChange={(e) => setCode(e.target.value.toUpperCase())}
              placeholder="VOTRE CODE"
              maxLength={6}
              className={styles.codeInput}
              autoFocus
            />
          </div>

          <button type="submit" disabled={loading} className={styles.authButton}>
            {loading ? 'Vérification...' : 'Continuer'}
          </button>
        </form>

        <div className={styles.authFooter}>
          <p>Vous n'avez pas de code ? Contactez votre syndic.</p>
          <hr />
          <Link to="/login" className={styles.link}>
            Se connecter avec email/mot de passe
          </Link>
        </div>
      </div>
    </div>
  );
};

export default LoginWithCodePage;
