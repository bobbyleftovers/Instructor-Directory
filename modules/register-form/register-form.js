import {
  insertError,
  clearErrors,
  isValidEmail
} from '../../src/js/lib/forms'

export default el => {
  // const form = document.querySelector('.register-form')
  const validateForm = (submitted = false) => {
    const submitBtn = document.querySelector('.registration__input--submit input')
    const fname = document.querySelector('.registration__input--fname input')
    const lname = document.querySelector('.registration__input--lname input')
    const email = document.querySelector('.registration__input--email input')
    const pass = document.querySelector('.registration__input--pass input')
    let hasErrors = false

    clearErrors('.registration__input--fname')
    if (fname.classList.contains('touched') || submitted) {
      if (fname.value === '' || !fname.value) {
        insertError('* This field is required', fname)
        hasErrors = true
      }
    }

    clearErrors('.registration__input--lname')
    if (lname.classList.contains('touched') || submitted) {
      if (lname.value === '' || !lname.value) {
        insertError('* This field is required', lname)
        hasErrors = true
      }
    }

    clearErrors('.registration__input--email')
    if (email.classList.contains('touched') || submitted) {
      if (email.value === '' || !email.value) {
        insertError('* This field is required', email)
        hasErrors = true
      } else if (!isValidEmail(email.value)) {
        insertError('* Email is invalid', email)
        hasErrors = true
      }
    }

    clearErrors('.registration__input--pass')
    if (pass.classList.contains('touched') || submitted) {
      if (pass.value === '' || !pass.value) {
        insertError('* This field is required', pass)
        hasErrors = true
      } else if (pass.value.length < 8) {
        insertError('* Password must be a minimum of 8 characters')
      }
    }

    if (hasErrors) {
      submitBtn.disabled = true
    } else {
      submitBtn.disabled = false
    }

    return hasErrors
  }

  document.querySelectorAll('.registration__input.validate input').forEach(input => {
    input.addEventListener('focusout', (e) => {
      e.target.classList.add('touched')
      validateForm()
    })
  })
  // form.addEventListener('submit', function (e) {
  //   e.preventDefault()
  //   const continueSubmit = validateForm()
  //   if (continueSubmit) {
  //     form.submit(true)
  //   } else {
  //     return false
  //   }
  // })
}
