import { getTopOffset } from './dom'
import { debounce } from './utils'
import getTarget from './get-hash-target.js'

function map (arr = [], f = () => {}) {
  return Array.prototype.map.call(arr, f)
}

function getSection (a) {
  const target = getTarget(a)

  if (target) {
    return {
      offset: getTopOffset(target) - 150,
      el: target,
      anchor: a
    }
  }
}

function getSections (el) {
  let sections = {}
  map(el.querySelectorAll('a'), a => {
    const section = getSection(a)

    if (section) {
      sections[section.el.id] = section
    }
  })

  return sections
}

function findActiveSection (sections) {
  const y = window.pageYOffset
  let activeSection

  for (let i in sections) {
    if (y >= sections[i].offset && (!activeSection || sections[i].offset >= activeSection.offset)) {
      activeSection = sections[i]
    }
  }

  return activeSection
}

function toggleActiveAnchor (sections, activeSection) {
  for (let i in sections) {
    if (sections[i] === activeSection) {
      activeSection.anchor.classList.add('is-active')
    } else {
      sections[i].anchor.classList.remove('is-active')
    }
  }
}

/**
 * @param {HTMLElement} el Container element containing <a> tags with local anchors
 */
export default function scrollSpy (el) {
  let sections = getSections(el)
  const db = debounce(() => {
    toggleActiveAnchor(sections, findActiveSection(sections))
  }, 20)

  window.addEventListener('scroll', db)
}
