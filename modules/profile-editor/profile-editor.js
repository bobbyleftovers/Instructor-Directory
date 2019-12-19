import {
  insertError,
  isPhone,
  clearErrors,
  isValidEmail,
  isURL
} from '../../src/js/lib/forms'
import Cleave from 'cleave.js'
export default el => {
  const inputs = [
    ...document.querySelectorAll('.validate-input'),
    ...document.querySelectorAll('.acf-row .acf-fields .is-required .acf-input input')
  ]
  inputs.forEach((input) => {
    input.addEventListener('focusout', (e) => { validateForm() })
  })

  // Add a formatter for phone numbers
  const phoneFormatter = new Cleave('.input-wrap__phone input.input', { // eslint-disable-line
    numericOnly: true,
    blocks: [0, 3, 0, 3, 4],
    delimiters: ['(', ')', ' ', '-']
  })

  const validateForm = () => {
    let hasErrors = false
    document.querySelector('#profile-submit').disabled = false

    const fname = document.querySelector('.input-wrap__first-name input')
    clearErrors('.input-wrap__first-name')
    if (fname.value === '' || !fname.value) {
      insertError('* This field is required', fname)
      hasErrors = true
    }
    const lname = document.querySelector('.input-wrap__last-name input')
    clearErrors('.input-wrap__last-name')
    if (lname.value === '' || !lname.value) {
      insertError('* This field is required', lname)
      hasErrors = true
    }
    const email = document.querySelector('.input-wrap__email input')
    clearErrors('.input-wrap__email')
    if (email.value === '' || !email.value) {
      insertError('This field is required', email)
      hasErrors = true
    } else if (!isValidEmail(email.value)) {
      insertError('* Email is invalid', email)
      hasErrors = true
    }
    const phone = document.querySelector('.input-wrap__phone input')
    clearErrors('.input-wrap__phone')
    if (phone.value !== '' && !isPhone(phone.value)) {
      insertError('* Must be a full phone number', phone)
      hasErrors = true
    }
    const about = document.querySelector('.input-wrap__about textarea')
    clearErrors('.input-wrap__about', 'textarea')
    if (about.value === '' || !about.value) {
      insertError('* This field is required', about)
      hasErrors = true
    }
    const studios = document.querySelector('.input-wrap__studios .acf-form-container .acf-input')
    const studioRows = document.querySelectorAll('.input-wrap__studios tbody tr')
    clearErrors('.input-wrap__studios .acf-form-container', '.acf-input', true)
    if (studioRows.length <= 1) {
      insertError('* You need at least one studio', studios)
      hasErrors = true
    }
    // const certifications = document.querySelectorAll('.input-wrap__certifications input:checked')
    // clearErrors('.input-wrap__certifications certification-inner', certifications)
    // if (certifications.length === 0) {
    //   insertError('Please select at least one certification level')
    // }
    const website = document.querySelector('.input-wrap__website input')
    clearErrors('.input-wrap__website')
    if (website.value !== '' && !isURL(website.value)) {
      insertError('* Your personal website must be a valid URL', website)
      hasErrors = true
    }
    const facebook = document.querySelector('.input-wrap__facebook input')
    clearErrors('.input-wrap__facebook')
    if (facebook.value !== '' && !isURL(facebook.value)) {
      insertError('* Your Facebook link must be a valid URL', facebook)
      hasErrors = true
    }
    const insta = document.querySelector('.input-wrap__instagram input')
    clearErrors('.input-wrap__instagram')
    if (insta.value !== '' && !isURL(insta.value)) {
      insertError('* Your Instagram link must be a valid URL', insta)
      hasErrors = true
    }
    const twitter = document.querySelector('.input-wrap__twitter input')
    clearErrors('.input-wrap__twitter')
    if (twitter.value !== '' && !isURL(twitter.value)) {
      insertError('* Your Twitter link must be a valid URL', twitter)
      hasErrors = true
    }
    const youtube = document.querySelector('.input-wrap__youtube input')
    clearErrors('.input-wrap__youtube')
    if (youtube.value !== '' && !isURL(youtube.value)) {
      insertError('* Your YouTube link must be a valid URL', youtube)
      hasErrors = true
    }

    if (hasErrors) {
      document.querySelector('#profile-submit').disabled = true
    }

    return hasErrors
  }

  const checkboxer = new Checkboxer(document.querySelectorAll('.input-wrap__certifications .input__checkbox'), 3)
  checkboxer.init()

  validateForm()
}

class Checkboxer {
  constructor (checkboxes, maxChecked) {
    this.checkboxes = checkboxes
    this.maxChecked = maxChecked
    this.numChecked = 0
  }
  init () {
    // set the init value for numCheck and enable/disable accordingly
    this.checkboxes.forEach((box, i) => {
      if (box.checked) {
        this.numChecked++
        if (this.numChecked >= this.maxChecked) {
          this.disableUnchecked()
        }
      }
    })

    // add click events
    this.addEvents()
  }
  handleClick (box) {
    if (!box.disabled) {
      this.numChecked = (box.checked) ? this.numChecked + 1 : this.numChecked - 1
      this.numChecked < 3 ? this.enableUnchecked() : this.disableUnchecked()
    }
  }
  disableUnchecked () {
    this.checkboxes.forEach((box, i) => {
      if (!box.checked) {
        box.disabled = true
      }
    })
  }
  enableUnchecked () {
    this.checkboxes.forEach((box, i) => {
      box.disabled = false
    })
  }
  addEvents () {
    this.checkboxes.forEach((box, i) => {
      box.addEventListener('click', (e) => {
        this.handleClick(e.target)
      })
    })
  }
}
