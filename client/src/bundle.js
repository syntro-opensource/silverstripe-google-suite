/**
 * broadcastConversionEvent - broadcast conversion event
 *
 * @param  {string} label the conversion label
 * @param  {string} id    the id of the ad account
 * @return {bool}
 */
function broadcastConversionEvent(label, id) {
  if (typeof (label) !== 'string') {
    console.error('no conversion label given'); // eslint-disable-line no-console
  }
  let convId = id;
  if (!convId) convId = window.ssgsuiteDefaultToken;
  // const callback = function callback() {
  //   if (url || typeof (url) !== 'undefined') {
  //     window.location = url;
  //   }
  // };
  gtag('event', 'conversion', { // eslint-disable-line no-undef
    send_to: `${convId}/${label}`,
    // event_callback: callback,
  });
  return false;
}

/**
 * hrefFromElement - get the href for external links from the element
 *
 * @param  {Node} element the element in question
 * @return {null|string}
 */
// function hrefFromElement(element) {
//   if (element.tagName && ['a', 'A'].includes(element.tagName) && element.attributes.href) {
//     return element.attributes.href.value;
//   }
//   return null;
// }

/**
 * appendClickHandler - appends a conversion event on the given event type
 *
 * @param  {string} eventName  the event to track
 * @param  {object} conversion the conversion from the config
 */
function appendHandler(eventName, conversion) {
  const elements = document.querySelectorAll(conversion.selector);
  for (let i = 0; i < elements.length; i += 1) {
    // const url = conversion.conversion_url || hrefFromElement(elements[i]);
    elements[i].addEventListener(eventName, () => broadcastConversionEvent(
      conversion.conversion_label,
      conversion.conversion_id,
      // url,
    ));
  }
}

/**
 * ssgsuiteTrackConv - activates the conversion tracking
 */
function ssgsuiteTrackConv() {
  const trackOnClick = window.ssgsuiteOnClick;
  const trackOnSubmit = window.ssgsuiteOnSubmit;
  for (let i = 0; i < trackOnClick.length; i += 1) {
    appendHandler('click', trackOnClick[i]);
  }
  for (let j = 0; j < trackOnSubmit.length; j += 1) {
    appendHandler('submit', trackOnSubmit[j]);
  }
}
window.ssgsuiteTrackConv = ssgsuiteTrackConv;
window.ssgsuiteTrackConv();
