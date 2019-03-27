class GoogleAnalytic {
    //Send product was clicked
    productWasClicked(element, productId, list = 'Product List') {
        productId = productId + ''
        let product = this.getProduct(productId)
        if (product) {
            let productToSet = this.getProductToSet(product)
            ga('ec:addProduct', productToSet);
            ga('ec:setAction', 'click', {list: list});
            ga('send', 'event', 'UX', 'click', list, {
                hitCallback: function () {
                    window.location = $(element).attr('href');
                }
            });

        } else {
            window.location = $(element).attr('href');
        }
    }

    //Get product from any object
    getProduct(productId) {
        productId = productId + ''
        let product = null
        if (GLOBAL_PRUDUCTS[productId] || CAROUSEL_PRODUCTS[productId]) {
            if (GLOBAL_PRUDUCTS[productId]) {
                if (GLOBAL_PRUDUCTS[productId]['refer_object']) {
                    product = window[GLOBAL_PRUDUCTS[productId]['refer_object']][productId]
                    if (typeof product === 'string') {
                        product = JSON.parse(product)
                    }
                } else {
                    product = GLOBAL_PRUDUCTS[productId]
                    if (typeof product === 'string') {
                        product = JSON.parse(product)
                    }
                }
            } else {
                product = CAROUSEL_PRODUCTS[productId]
                if (typeof product === 'string') {
                    product = JSON.parse(product)
                }
            }
        }

        return product
    }

    //Basic product information
    getProductToSet(product) {
        let productToSet = {
            'id': product.id,
            'name': product.name,
            'category': (product.product_categories && product.product_categories.length) ? product.product_categories[0].name : '',
            'position': 1, //TODO: ?????
            'brand': (product.brand && product.brand.name) ? product.brand.name : '',
            'price': product.price,
            [GLOBAL_SETTING.GA_CUSTOM_FIELD_ITEM_TYPE]: product.product_type,
            [GLOBAL_SETTING.GA_CUSTOM_FIELD_BUYER_ID]: product.buyer_id ? product.buyer_id : '',
            [GLOBAL_SETTING.GA_CUSTOM_FIELD_BUYER_NAME]: product.buyer_name ? product.buyer_name : '',
            [GLOBAL_SETTING.GA_CUSTOM_FIELD_SUPPLIER_ID]: product.supplier_id,
            [GLOBAL_SETTING.GA_CUSTOM_FIELD_SUPPLIER_NAME]: product.supplier_name
        }

        return productToSet
    }

    //Send  add to cart
    addToCart(product, qty) {
        let productToSet = this.getProductToSet(product)
        productToSet.quantity = qty
        productToSet.price = product.price

        ga('ec:addProduct', productToSet)
        ga('ec:setAction', 'add');
        ga('send', 'event', 'UX', 'click', 'add to cart');

    }

    //Send product remove from cart
    removeFromCart(product, qty) {
        let productToSet = this.getProductToSet(product)
        productToSet.quantity = qty
        productToSet.price = product.price

        ga('ec:addProduct', productToSet)
        ga('ec:setAction', 'remove');
        ga('send', 'event', 'UX', 'click', 'remove from cart');

    }

    //Send add to wish list
    addToWishList(product) {
        let productToSet = this.getProductToSet(product)
        productToSet.quantity = 1
        productToSet.price = product.price

        ga('ec:addProduct', productToSet)
        ga('ec:setAction', 'add');
        ga('send', 'event', 'UX', 'click', 'add to wish list');

    }

    //Send remove from wish list
    removeFromWishList(product) {
        let productToSet = this.getProductToSet(product)
        productToSet.quantity = 1
        productToSet.price = product.price

        ga('ec:addProduct', productToSet)
        ga('ec:setAction', 'remove');
        ga('send', 'event', 'UX', 'click', 'remove from wish list');

    }

    //Send impression products
    impressionProducts(sendPageView = true) {
        const impressions = this.getImpressionProducts()
        let products = []
        for (let i = 0; i < impressions.length; ++i) {
            const list = impressions[i].name
            for (let j = 0; j < impressions[i].items.length; ++j) {
                let product = this.getProductToSet(impressions[i].items[j])
                product.list = list
                products.push(product)
                ga('ec:addImpression', product)
            }
        }
        if (sendPageView) {
            if (products && products.length > 0) {
                ga('send', 'pageview');
            }
        } else {
            return products
        }
    }

    //Prepare impression products
    getImpressionProducts() {
        let impressions = []
        if (IMPRESSION_PRODUCTS && IMPRESSION_PRODUCTS.length > 0) {
            for (let i = 0; i < IMPRESSION_PRODUCTS.length; ++i) {
                const list = IMPRESSION_PRODUCTS[i].name
                let products = []
                for (let productId in IMPRESSION_PRODUCTS[i].items) {
                    let product = IMPRESSION_PRODUCTS[i].items[productId]
                    if (product['refer_object']) {
                        products.push(this.getProduct(productId))
                    } else {
                        products.push(product)
                    }
                }

                impressions.push({
                    name: list,
                    items: products
                })
            }
        }

        return impressions;
    }

    //Send product detail was viewed
    productDetail() {
        if (GLOBAL_SETTING.is_loaded_product_detail === 1
            /*&& GLOBAL_SETTING.is_loaded_related_products === 1
            && GLOBAL_SETTING.is_loaded_recent_products === 1*/) {
            let productId = GLOBAL_SETTING.product_id + ''
            let productToSet = this.getProductToSet(this.getProduct(productId))
            ga('ec:addProduct', productToSet)

            const products = this.impressionProducts(false)
            ga('ec:setAction', 'detail');
            ga('send', 'pageview');
        }

    }

    //Transaction data
    getTransactionData(data) {
        let transaction = {
            'id': data.id ? data.id : '',
            [GLOBAL_SETTING.GA_CUSTOM_FIELD_STORE_ID]: data.store_id ? data.store_id : '',
            [GLOBAL_SETTING.GA_CUSTOM_FIELD_STORE_AREA]: data.store_area ? data.store_area : '',
            [GLOBAL_SETTING.GA_CUSTOM_FIELD_PAYMENT_TYPE]: data.payment_type ? data.payment_type : '',
            [GLOBAL_SETTING.GA_CUSTOM_FIELD_SHIPPING_TYPE]: data.shipping_type ? data.shipping_type : '',
            [GLOBAL_SETTING.GA_CUSTOM_FIELD_COST]: data.cost ? data.cost : '',
            [GLOBAL_SETTING.GA_CUSTOM_FIELD_MAKRO_MEMBER_ID]: data.makro_member_id ? data.makro_member_id : '',
            [GLOBAL_SETTING.GA_CUSTOM_FIELD_MAKRO_CUSTOMER_GROUP_ID]: data.makro_customer_group_id ? data.makro_customer_group_id : '',
            'revenue': data.revenue ? data.revenue : '',
            'tax': data.tax ? data.tax : '',
            'shipping': data.shipping ? data.shipping : ''
        }

        return transaction
    }

    //Checkout Step 0  (Payment landing)
    checkoutPaymentLanding(products, callback) {
        //Add products
        for (let i = 0; i < products.length; ++i) {
            let productToSet = this.getProductToSet(products[i])
            productToSet.price = products[i].price
            productToSet.quantity = products[i].qty
            ga('ec:addProduct', productToSet)
        }

        let option = {
            'step': 1,
            'option': 'Payment landing'
        }

        ga('ec:setAction', 'checkout', option);

        ga('send', 'event', 'Checkout', 'Checkout', {
            hitCallback: function () {
                callback()
            }
        })
    }

    //Checkout Step 1 (Payment)
    checkoutPayment(products, paymentType, callback) {
        //Add products
        for (let i = 0; i < products.length; ++i) {
            let productToSet = this.getProductToSet(products[i])
            productToSet.price = products[i].price
            productToSet.quantity = products[i].qty
            ga('ec:addProduct', productToSet)
        }

        let option = {
            'step': 2,
            'option': paymentType
        }

        ga('ec:setAction', 'checkout', option);

        ga('send', 'event', 'Checkout', 'Option', {
            hitCallback: function () {
                callback()
            }
        })
    }

    //Checkout Step 2 (Shipping)
    checkoutShipping(products, shippingType, callback) {
        //Add products
        for (let i = 0; i < products.length; ++i) {
            let productToSet = this.getProductToSet(products[i])
            productToSet.price = products[i].price
            productToSet.quantity = products[i].qty
            ga('ec:addProduct', productToSet)
        }

        let option = {
            'step': 3,
            'option': 'Pickup at Makro Store'
        }

        ga('ec:setAction', 'checkout', option);

        ga('send', 'event', 'Checkout', 'Option', {
            hitCallback: function () {
                callback()
            }
        })
    }

    //Final checkout
    purchase(data, products, callback = null) {
        let transaction = this.getTransactionData(data)
        const productGroup = JSON.parse(products)
        let newProducts = []
        for (let i in productGroup) {
            let data = productGroup[i]
            data.price = productGroup[i].price
            data.qty = productGroup[i].quantity
            newProducts.push(data)
        }

        transaction.step = 4
        transaction.option = data.payment_type

        let productTransaction = {
            id: transaction.id
        }

        let chunkProducts = _.chunk(newProducts, 10)
        chunkProducts.forEach((products) => {
            products.forEach((product) => {
                product.buyer_id = data.buyer_id
                product.buyer_name = data.buyer_name
                let productToSet = this.getProductToSet(product)
                productToSet.price = product.price
                productToSet.quantity = product.qty
                ga('ec:addProduct', productToSet)
            })

            ga('ec:setAction', 'purchase', productTransaction);
            ga('send', 'event', 'Checkout', 'Purchase', 'items batch');
        })

        ga('ec:setAction', 'purchase', transaction);
        ga('send', 'event', 'Checkout', 'Purchase', 'transaction details', {
            hitCallback: function () {
                console.log('GA send Checkout/Purchase')
                if (callback) {
                    callback()
                }
            }
        })
    }

    //Send transaction fail
    refund() {

    }
}

export default GoogleAnalytic
