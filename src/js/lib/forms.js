import 'nodelist-foreach-polyfill'
import {
  appendNode,
  appendHtml,
  remove
  // getChildren
} from './dom'

/**
 * Return true if email is valid
 *
 * @param {String} email - The string to validate
 */
const isValidEmail = (email) => {
  const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  return re.test(String(email).toLowerCase())
}

/**
 * Return true if phone number is valid
 *
 * @param {String} phone - The string to validate
 */
const isPhone = (phone) => {
  return phone.length === 14
}

/**
 * Return true if URL is valid
 *
 * @param {String} url - The string to validate
 */
const isURL = (url) => {
  const pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
    '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
    '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
    '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
    '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
    '(\\#[-a-z\\d_]*)?$', 'i') // fragment locator
  return pattern.test(url)
}

/**
 * Insert an error span after an input
 *
 * @param {String} errorMsg - The message to show
 * @param {HTMLElement} input - The element to insert it after
 */
const insertError = (errorMsg, input) => {
  const span = document.createElement('span')
  appendHtml(span, errorMsg)
  span.classList.add('error')
  span.classList.add('form-error__span')
  input.classList.add('form-error__input')
  appendNode(input, span)
}

/**
 * We use <label> tags instead of input placeholders to better align with ADA standards
 * This function is used to toggle a class on label tags to control their visibilty
 *
 * @param {HTMLElement} e - The element to clear (probably a wrapping element)
 */
const clearErrors = (selector, type = 'input') => {
  const spans = document.querySelectorAll(`${selector} .form-error__span`)
  const input = document.querySelector(`${selector} ${type}`)
  input.classList.remove('form-error__input')
  spans.forEach((span) => {
    remove(span)
  })
}

export {
  insertError,
  clearErrors,
  isValidEmail,
  isPhone,
  isURL
}
