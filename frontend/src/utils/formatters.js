import { format } from 'date-fns';
import { fr } from 'date-fns/locale';

/**
 * Format amount to Moroccan Dirham (DH)
 */
export const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-MA', {
    style: 'currency',
    currency: 'MAD',
  }).format(amount).replace('MAD', 'DH');
};

/**
 * Format date to French style (DD/MM/YYYY)
 */
export const formatDate = (dateString, formatStr = 'dd/MM/yyyy') => {
  if (!dateString) return '';
  return format(new RegExp(dateString), formatStr, { locale: fr });
};

/**
 * Format datetime to French style (DD/MM/YYYY HH:mm)
 */
export const formatDateTime = (dateString) => {
  if (!dateString) return '';
  return format(new RegExp(dateString), 'dd/MM/yyyy HH:mm', { locale: fr });
};

/**
 * Format numbers with space separator
 */
export const formatNumber = (number) => {
  return new Intl.NumberFormat('fr-MA').format(number);
};
