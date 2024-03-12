var currencyInputs = document.querySelectorAll('input[type="currency"]')
var currency = 'MXN' // https://www.currency-iso.org/dam/downloads/lists/list_one.xml
var previousValue = '';

// format initial values
currencyInputs.forEach(function(input) {
  onBlur({ target: input })
})

// bind event listeners
currencyInputs.forEach(function(input) {
  input.addEventListener('focus', onFocus)
  input.addEventListener('blur', onBlur)
})

function localStringToNumber(s) {
    var num = Number(String(s).replace(/[^0-9.,-]+/g, ""));
    return isNaN(num) ? 0 : num;
  }

  function onFocus(e) {
    previousValue = e.target.value;
  }


function onBlur(e) {
  var value = e.target.value;
  if (!value) {
    e.target.value = previousValue;
    return;
  }

  var options = {
      maximumFractionDigits : 2,
      currency              : currency,
      style                 : "currency",
      currencyDisplay       : "symbol"
  }
  
  e.target.value = localStringToNumber(value).toLocaleString(undefined, options);
}