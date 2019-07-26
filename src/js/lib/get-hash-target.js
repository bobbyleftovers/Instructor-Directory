/**
 * @param {HTMLElement} anchor Target element to bind <a> clicks to
 * @return {HTMLElement}
 */
function getTarget (anchor) {
  const hash = anchor.hash || false
  const query = hash && hash.length ? document.querySelectorAll(hash) : null
  const target = query && query.length ? query[0] : null

  return target || null
}

export default getTarget
