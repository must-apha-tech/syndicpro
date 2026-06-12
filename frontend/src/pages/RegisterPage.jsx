import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';
import styles from '../components/Auth/LoginForm.module.css'; // Reusing styles

const getErrorMessage = (error) => {
  const responseData = error?.response?.data;
  const details = responseData?.data;

  if (details) {
    const messages = Array.isArray(details)
      ? details.flat(Infinity)
      : Object.values(details).flat(Infinity);

    if (messages.length) {
      return messages.filter(Boolean).join(' • ');
    }
  }

  return responseData?.message || 'Erreur lors de la création du compte';
};

const RegisterPage = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    phone: '',
  });
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState('');
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsLoading(true);
    setError('');
    
    try {
      await api.post('/auth/register', formData);
      navigate('/login', { state: { message: 'Compte créé avec succès ! Veuillez vous connecter.' } });
    } catch (err) {
      setError(getErrorMessage(err));
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className={styles.container}>
      <form className={styles.form} onSubmit={handleSubmit}>
        <h2 className={styles.title}>Créer un compte</h2>
        
        {error && <div className={styles.error}>{error}</div>}
        
        <div className={styles.field}>
          <label>Nom complet</label>
          <input
            type="text"
            value={formData.name}
            onChange={(e) => setFormData({...formData, name: e.target.value})}
            required
          />
        </div>

        <div className={styles.field}>
          <label>Email</label>
          <input
            type="email"
            value={formData.email}
            onChange={(e) => setFormData({...formData, email: e.target.value})}
            required
          />
        </div>
        
        <div className={styles.field}>
          <label>Téléphone</label>
          <input
            type="text"
            value={formData.phone}
            onChange={(e) => setFormData({...formData, phone: e.target.value})}
          />
        </div>

        <div className={styles.field}>
          <label>Mot de passe</label>
          <input
            type="password"
            value={formData.password}
            onChange={(e) => setFormData({...formData, password: e.target.value})}
            required
          />
          <div style={{ fontSize: '0.85rem', color: '#6b7280', marginTop: '0.35rem' }}>
            8 caractères minimum, avec une majuscule, un chiffre et un symbole.
          </div>
        </div>
        
        <button type="submit" className={styles.submit} disabled={isLoading}>
          {isLoading ? 'Enregistrement...' : 'S\'inscrire'}
        </button>
        
        <div className={styles.footer}>
          Déjà un compte ? 
          <button type="button" onClick={() => navigate('/login')} className={styles.link}>
            Se connecter
          </button>
        </div>
      </form>
    </div>
  );
};

export default RegisterPage;
