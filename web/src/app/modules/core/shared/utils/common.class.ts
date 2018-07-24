export class Common {
  static buildQueryParameters(obj, obj2 = {}) {
    if (obj === {} && obj2 === {}) {
      return '';
    }
    const str = [];
    for (const p in obj) {
      if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]));
      }
    }
    for (const p in obj2) {
      if (obj2.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj2[p]));
      }
    }
    const rtn = str.join('&');
    return rtn ? '?' + rtn : '';

  }
}
