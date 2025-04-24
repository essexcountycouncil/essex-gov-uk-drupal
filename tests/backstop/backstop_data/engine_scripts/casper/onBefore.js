module.exports = function (casper, scenario, vp) {
  require('./loadCookies')(casper, scenario);
  casper.userAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36');
};
