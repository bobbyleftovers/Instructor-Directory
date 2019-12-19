import {
  insertError,
  clearErrors,
  isValidEmail
} from '../../src/js/lib/forms'

export default el => {
  const usernameInput = document.querySelector('#user_login')

  const validateForm = () => {
    const submitBtn = document.querySelector('.password-reset__input--submit .lostpassword-button')
    let hasErrors = false

    clearErrors('.password-reset__input--user')
    if (usernameInput.classList.contains('touched')) {
      if (usernameInput.value === '' || !usernameInput.value) {
        insertError('* This field is required', usernameInput)
        hasErrors = true
      } else if (!isValidEmail(usernameInput.value)) {
        insertError('* Email is invalid', usernameInput)
        hasErrors = true
      }
    }

    if (hasErrors) {
      submitBtn.disabled = true
    } else {
      submitBtn.disabled = false
    }
  }

  document.querySelector('#user_login').addEventListener('focusout', (e) => {
    e.target.classList.add('touched')
    validateForm()
  })
}
