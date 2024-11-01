if (!String.prototype.startsWith) {
  Object.defineProperty(String.prototype, 'startsWith', {
    enumerable: false,
    configurable: false,
    writable: false,
    value: function(searchString, position) {
      position = position || 0;
      return this.indexOf(searchString, position) === position;
    }
  });
}

function doBuyTokens(e) {
	e.preventDefault();
	web3metamask.eth.getAccounts(function (err, accounts) {

		if (err) {
			console.log(err);
		}

		if (0 == accounts.length) {
			console.log("Metamask account not found");
			alert("Sign in into your metamask account please.");
			return;
		}

		var v = jQuery("#etherInput").val();
		if ("" == v) {
			v = "0.2";
	  }
		var val = decimals * parseFloat(v)

		var address = accounts[0];
		var transactionObject = {
			from: address,
			to: crowdsaleAddress,
			value: val,
			gas: '200000',
			data: txData,
			nonce: '0x00'
		}
		web3.eth.getTransactionCount(address, function(err, res) {
			if (err) {
				console.log(err);
				console.log("Network error. Check your infuraApiKey settings.");
			} else {
				console.log("Current address nonce value: ", res);
				nonce = parseInt(res);
				transactionObject.nonce = "0x" + nonce.toString(16);
				console.log(transactionObject);
				web3metamask.eth.sendTransaction(transactionObject, function (err, transactionHash) {
					if (err) {
						console.log(err);
						alert("You have rejected the token buy operation.");
					}
					if (!err) {
						console.log(transactionHash);
						alert("You have successfully bought a treethereum tree. Please wait up to 10 minutes for your tree to load on the site. Transaction hash: " + transactionHash);
                        window.location.replace('https://www.treethereum.com/new-tree/');
					}
				})
			}
		});

	})
}

function changeEtherAmount() {
	var v = jQuery("#etherInput").val();
	if ("" == v) {
		v = "0.2";
	}
	var val = parseFloat(v);
  var treeth = 0;
  if (val >= 0.2) {
    var treeth = 1;
    }
	jQuery("#rateToken").text((treeth));

	if (rateData) {
		jQuery("#rateUSD").text((val * parseFloat(rateData.ethusd)).toFixed(2));
	}
}

jQuery(document).ready(function () {
    changeEtherAmount()
	if ("undefined" !== typeof window["web3Endpoint"]) {
		jQuery('.treethereum-quantity').each(function () {
			var spinner = jQuery(this),
                    input = 0.2,
					min = 0.2;
					max = 0.2;
			step = parseFloat(input.attr('step'));

		});

		if (window.web3) {
			window.web3metamask = window.web3;
			window.web3 = new Web3(new Web3.providers.HttpProvider(web3Endpoint))
			jQuery("#buytreethereumButton").click(doBuyTokens);
		} else {
			jQuery("#buytreethereumButton").text("Install Metamask!");
			jQuery("#buytreethereumButton").attr("href", "https://metamask.io/");
			jQuery("#buytreethereumButton").attr("target", "_blank");
		}

//		jQuery("a#addressForSelect").click(copyAddress);


		jQuery("#etherInput").change(changeEtherAmount);

	}
});
