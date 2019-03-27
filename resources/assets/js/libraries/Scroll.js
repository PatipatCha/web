class Scroll {

    bottom(elem) {
        elem.scrollTop = elem.scrollHeight;
    }

    top(elem) {
        elem.scrollTop = 0;
    }

    to(elem, alignToTop = false) {
        elem.scrollIntoView(alignToTop)
    }
}

export default Scroll