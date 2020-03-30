var config = {
  deps: [
    "js/custom",
    "js/myaddtocart"
  ],
  paths:{
    'slick': 'js/slick.min'
  },
  shim: {
    slick: {
      deps: ['jquery']
    }
  }
};