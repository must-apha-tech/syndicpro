import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import styles from './RegisterWithCodeForm.module.css';

const RegisterWithCodeForm = () => {
  const navigate = useNavigate();
  const [step, setStep] = useState(1);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  
  // Form State
  const [code, setCode] = useState('');
  const [residenceInfo, setResidenceInfo] = useState(null);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    acceptTerms: false
  });

  // Password Validation
  const passwordRequirements = {
    length: formData.password.length >= 8,
    uppercase: /[A-Z]/.test(formData.password),
    number: /[0-9]/.test(formData.password),
    special: /[!@#$%^&*]/.test(formData.password)
  };

  const getPasswordStrength = () => {
    const met = Object.values(passwordRequirements).filter(Boolean).length;
    if (met <= 1) return { label: 'Faible', color: '#ff4d4f', width: '25%' };
    if (met <= 3) return { label: 'Moyen', color: '#faad14', width: '60%' };
    return { label: 'Fort', color: '#52c41a', width: '100%' };
  };

  const validateCode = async (e) => {
    e.preventDefault();
    if (code.length !== 6) {
      setError('Le code doit comporter 6 caractères');
      return;
    }
    
    setLoading(true);
    setError('');
    
    try {
      const response = await axios.post('/api/auth/validate-code', { code });
      setResidenceInfo(response.data);
      setStep(2);
    } catch (err) {
      setError(err.response?.data?.error || 'Code invalide ou expiré');
    } finally {
      setLoading(false);
    }
  };

  const handleRegister = async (e) => {
    e.preventDefault();
    
    // Client-side validation
    if (!passwordRequirements.length || !passwordRequirements.uppercase || !passwordRequirements.number || !passwordRequirements.special) {
      setError('Veuillez respecter toutes les exigences de mot de passe');
      return;
    }
    if (formData.password !== formData.password_confirmation) {
      setError('Les mots de passe ne correspondent pas');
      return;
    }
    if (!formData.acceptTerms) {
      setError('Vous devez accepter les conditions d\'utilisation');
      return;
    }

    setLoading(true);
    setError('');

    try {
      const response = await axios.post('/api/auth/register-with-code', {
        ...formData,
        code
      });
      
      localStorage.setItem('token', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
      navigate('/dashboard');
    } catch (err) {
      setError(err.response?.data?.error || 'Une erreur est survenue lors de l\'inscription');
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }));
  };

  const strength = getPasswordStrength();

  return (
    <div className={styles.container}>
      <div className={styles.card}>
        <div className={styles.header}>
          <h2>Inscription Copropriétaire</h2>
          <div className={styles.steps}>
            <div className={`${styles.step} ${step >= 1 ? styles.active : ''}`}>1</div>
            <div className={`${styles.stepLine} ${step >= 2 ? styles.active : ''}`}></div>
            <div className={`${styles.step} ${step >= 2 ? styles.active : ''}`}>2</div>
            <div className={`${styles.stepLine} ${step >= 3 ? styles.active : ''}`}></div>
            <div className={`${styles.step} ${step >= 3 ? styles.active : ''}`}>3</div>
          </div>
          <p className={styles.stepTitle}>
            {step === 1 && "Vérification du code"}
            {step === 2 && "Confirmation du lot"}
            {step === 3 && "Informations personnelles"}
          </p>
        </div>

        {error && <div className={styles.errorAlert}>{error}</div>}

        {step === 1 && (
          <form onSubmit={validateCode} className={styles.form}>
            <div className={styles.inputGroup}>
              <label htmlFor="code">Code d'accès</label>
              <input
                id="code"
                type="text"
                value={code}
                onChange={(e) => setCode(e.target.value.toUpperCase())}
                placeholder="Ex: ABC123"
                maxLength={6}
                required
                className={styles.codeInput}
              />
              <p className={styles.hint}>Entrez le code à 6 caractères fourni par votre syndic.</p>
            </div>
            <button type="submit" disabled={loading} className={styles.button}>
              {loading ? 'Vérification...' : 'Valider le code'}
            </button>
          </form>
        )}

        {step === 2 && residenceInfo && (
          <div className={styles.infoStep}>
            <div className={styles.infoCard}>
              <h3>{residenceInfo.residence.name}</h3>
              <p>{residenceInfo.residence.address}, {residenceInfo.residence.city}</p>
              <hr />
              <div className={styles.lotDetails}>
                <div><strong>Lot n°:</strong> {residenceInfo.lot.numero}</div>
                <div><strong>Type:</strong> {residenceInfo.lot.type}</div>
                <div><strong>Surface:</strong> {residenceInfo.lot.surface} m²</div>
                <div><strong>Quote-part:</strong> {residenceInfo.lot.quote_part} / 1000</div>
              </div>
            </div>
            <p className={styles.confirmMsg}>Confirmez-vous l'enregistrement pour ce lot ?</p>
            <div className={styles.buttonGroup}>
              <button 
                onClick={() => setStep(1)} 
                className={styles.secondaryButton}
              >
                Non, changer de code
              </button>
              <button 
                onClick={() => setStep(3)} 
                className={styles.button}
              >
                Oui, continuer
              </button>
            </div>
          </div>
        )}

        {step === 3 && (
          <form onSubmit={handleRegister} className={styles.form}>
            <div className={styles.inputGroup}>
              <label htmlFor="name">Nom complet</label>
              <input
                id="name"
                name="name"
                type="text"
                value={formData.name}
                onChange={handleInputChange}
                required
              />
            </div>
            <div className={styles.inputGroup}>
              <label htmlFor="email">Email</label>
              <input
                id="email"
                name="email"
                type="email"
                value={formData.email}
                onChange={handleInputChange}
                required
              />
            </div>
            <div className={styles.inputGroup}>
              <label htmlFor="phone">Téléphone (Optionnel)</label>
              <input
                id="phone"
                name="phone"
                type="tel"
                value={formData.phone}
                onChange={handleInputChange}
              />
            </div>
            <div className={styles.inputGroup}>
              <label htmlFor="password">Mot de passe</label>
              <input
                id="password"
                name="password"
                type="password"
                value={formData.password}
                onChange={handleInputChange}
                required
              />
              <div className={styles.strengthBar}>
                <div 
                  className={styles.strengthFill} 
                  style={{ width: strength.width, backgroundColor: strength.color }}
                ></div>
              </div>
              <p className={styles.strengthText}>Force: {strength.label}</p>
            </div>
            
            <div className={styles.requirements}>
              <div className={passwordRequirements.length ? styles.met : ''}>
                {passwordRequirements.length ? '✓' : '○'} Min. 8 caractères
              </div>
              <div className={passwordRequirements.uppercase ? styles.met : ''}>
                {passwordRequirements.uppercase ? '✓' : '○'} Une majuscule
              </div>
              <div className={passwordRequirements.number ? styles.met : ''}>
                {passwordRequirements.number ? '✓' : '○'} Un chiffre
              </div>
              <div className={passwordRequirements.special ? styles.met : ''}>
                {passwordRequirements.special ? '✓' : '○'} Un caractère spécial (!@#$)
              </div>
            </div>

            <div className={styles.inputGroup}>
              <label htmlFor="password_confirmation">Confirmer le mot de passe</label>
              <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                value={formData.password_confirmation}
                onChange={handleInputChange}
                required
              />
            </div>

            <div className={styles.checkboxGroup}>
              <input
                id="acceptTerms"
                name="acceptTerms"
                type="checkbox"
                checked={formData.acceptTerms}
                onChange={handleInputChange}
                required
              />
              <label htmlFor="acceptTerms">J'accepte les Conditions d'Utilisation</label>
            </div>

            <button type="submit" disabled={loading} className={styles.button}>
              {loading ? 'Création du compte...' : 'S\'enregistrer'}
            </button>
          </form>
        )}
      </div>
    </div>
  );
};

export default RegisterWithCodeForm;
