import {
  insertError,
  clearErrors,
  isValidEmail
} from '../../src/js/lib/forms'
import {
  setAttribute
} from '../../src/js/lib/dom'

export default el => {
  const usernameInput = document.querySelector('#login__user')
  const passwordInput = document.querySelector('#login__pass')
  setAttribute('placeholder', 'Email Address', usernameInput)
  setAttribute('placeholder', 'Password', passwordInput)

  const validateForm = (submitted = false) => {
    // const usernameWrap = document.querySelector('.login-username')
    // const passwordWrap = document.querySelector('.login-password')
    const submitBtn = document.querySelector('#wp-submit')
    let hasErrors = false

    clearErrors('.login-username')
    if (usernameInput.classList.contains('touched') || submitted) {
      if (usernameInput.value === '' || !usernameInput.value) {
        insertError('* This field is required', usernameInput)
        hasErrors = true
      } else if (!isValidEmail(usernameInput.value)) {
        insertError('* Email is invalid', usernameInput)
        hasErrors = true
      }
    }

    clearErrors('.login-password')
    if (passwordInput.classList.contains('touched') || submitted) {
      if (passwordInput.value === '' || !passwordInput.value) {
        insertError('*  This field is required', passwordInput)
        hasErrors = true
      }
    }

    if (hasErrors) {
      submitBtn.disabled = true
    } else {
      submitBtn.disabled = false
    }
  }

  document.querySelectorAll('#login__form .input').forEach(input => {
    input.addEventListener('focusout', (e) => {
      e.target.classList.add('touched')
      validateForm()
    })
  })
}
