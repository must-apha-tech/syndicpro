import React from 'react';
import { useForm } from 'react-hook-form';
import styles from './Comptabilite.module.css';

const AppelDeFondsForm = ({ exercices, lots, onSubmit, onCancel, isLoading }) => {
  const { register, handleSubmit, formState: { errors } } = useForm({
    defaultValues: {
      exercice_id: '',
      lot_id: '',
      amount_total: '',
      date_emission: new Date().toISOString().split('T')[0],
      date_echeance: '',
    }
  });

  return (
    <form onSubmit={handleSubmit(onSubmit)} className={styles.form}>
      <h3>Générer un Appel de Fonds</h3>
      
      <div className={styles.field}>
        <label>Exercice Comptable</label>
        <select {...register('exercice_id', { required: 'Requis' })}>
          <option value="">Sélectionner...</option>
          {exercices.map(ex => (
            <option key={ex.id} value={ex.id}>{ex.annee} ({ex.statut})</option>
          ))}
        </select>
      </div>

      <div className={styles.field}>
        <label>Lot (Optionnel - Laisser vide pour TOUS les lots)</label>
        <select {...register('lot_id')}>
          <option value="">Tous les lots</option>
          {lots.map(lot => (
            <option key={lot.id} value={lot.id}>Lot {lot.numero} - {lot.proprietaire?.name}</option>
          ))}
        </select>
      </div>

      <div className={styles.field}>
        <label>Montant Total à répartir</label>
        <input type="number" {...register('amount_total', { required: 'Requis' })} />
      </div>

      <div className={styles.grid}>
        <div className={styles.field}>
          <label>Date d'émission</label>
          <input type="date" {...register('date_emission')} />
        </div>
        <div className={styles.field}>
          <label>Date d'échéance</label>
          <input type="date" {...register('date_echeance', { required: 'Requis' })} />
        </div>
      </div>

      <div className={styles.actions}>
        <button type="button" onClick={onCancel}>Annuler</button>
        <button type="submit" disabled={isLoading}>Générer</button>
      </div>
    </form>
  );
};

export default AppelDeFondsForm;
