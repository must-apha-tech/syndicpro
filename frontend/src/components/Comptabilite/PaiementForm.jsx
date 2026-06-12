import React from 'react';
import { useForm } from 'react-hook-form';
import styles from './Comptabilite.module.css';

const PaiementForm = ({ appel, onSubmit, onCancel, isLoading }) => {
  const { register, handleSubmit, formState: { errors } } = useForm({
    defaultValues: {
      appel_id: appel?.id,
      amount: appel?.reliquat || 0,
      date_paiement: new Date().toISOString().split('T')[0],
      mode: 'virement',
      reference: '',
    }
  });

  return (
    <form onSubmit={handleSubmit(onSubmit)} className={styles.form}>
      <h3>Enregistrer un Paiement</h3>
      <p>Appel n° {appel?.numero} - Reste à payer: {appel?.reliquat} DH</p>
      
      <div className={styles.field}>
        <label>Montant</label>
        <input 
          type="number" 
          step="0.01" 
          {...register('amount', { 
            required: 'Requis', 
            max: { value: appel?.reliquat, message: 'Excède le reste à payer' } 
          })} 
        />
        {errors.amount && <span className={styles.error}>{errors.amount.message}</span>}
      </div>

      <div className={styles.field}>
        <label>Mode de paiement</label>
        <select {...register('mode')}>
          <option value="virement">Virement</option>
          <option value="cheque">Chèque</option>
          <option value="especes">Espèces</option>
          <option value="en_ligne">En ligne</option>
        </select>
      </div>

      <div className={styles.field}>
        <label>Référence (ex: n° chèque)</label>
        <input {...register('reference', { required: 'Requis' })} />
      </div>

      <div className={styles.actions}>
        <button type="button" onClick={onCancel}>Annuler</button>
        <button type="submit" disabled={isLoading}>Confirmer le paiement</button>
      </div>
    </form>
  );
};

export default PaiementForm;
