export const emailRules = [
  (val: string) => !!val || 'Email requis',
  (val: string) => /.+@.+\..+/.test(val) || 'Email invalide'
]

export const passwordRules = [
  (val: string) => !!val || 'Code d\'accès requis',
  (val: string) => val.length >= 6 || 'Minimum 6 caractères'
]