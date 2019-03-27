import GoogleAnalytic from '../libraries/google_analytic'

const googleAnalytic = new GoogleAnalytic()

//On product was clicked (Global function)
window.gaOnProductWasClicked = googleAnalytic.productWasClicked.bind(googleAnalytic)

//Send impression products (Global function)
window.gaTriggerImpression = googleAnalytic.impressionProducts.bind(googleAnalytic)

//Send  product detail
window.gaProductDetailWasViewd = googleAnalytic.productDetail.bind(googleAnalytic)

//Purchase
window.gaPurchase = googleAnalytic.purchase.bind(googleAnalytic)
