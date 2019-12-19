import {
  insertError,
  clearErrors
} from '../../src/js/lib/forms'

export default el => {
  const pass1Input = document.querySelector('#pass1')
  const pass2Input = document.querySelector('#pass2')

  const validateForm = () => {
    const submitBtn = document.querySelector('#resetpass-button')
    let hasErrors = false

    clearErrors('.password-reset__input--pass1')
    if (pass1Input.classList.contains('touched')) {
      if (pass1Input.value === '' || !pass1Input.value) {
        insertError('* This field is required', pass1Input)
        hasErrors = true
      }
    }

    clearErrors('.password-reset__input--pass2')
    if (pass2Input.classList.contains('touched')) {
      if (pass2Input.value === '' || !pass2Input.value) {
        insertError('*  This field is required', pass2Input)
        hasErrors = true
      }
    }

    if (hasErrors) {
      submitBtn.disabled = true
    } else {
      submitBtn.disabled = false
    }
  }

  document.querySelectorAll('.password-reset__input .input').forEach(input => {
    input.addEventListener('focusout', (e) => {
      e.target.classList.add('touched')
      validateForm()
    })
  })
}
