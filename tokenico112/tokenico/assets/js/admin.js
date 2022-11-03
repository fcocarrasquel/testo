(($) => {

    let willNotBeChecked = [
        'token',
        'network',
        'networkId',
        'totalSales',
        'remainingLimit',
        'contractAddress',
        'contractVersion',
        'adminAddress'
    ];
    
    willNotBeChecked.forEach(val => {
        $("[name='presaleData["+val+"]']").closest('.csf-field').css('display', 'none');
    });

    if (Tokenico.presaleStatus != 'publish') {
        $(".network-name-content").css('display', 'none');
        $("[name='presaleData[networkName]']").closest('.csf-field').css('display', 'none');
        $("[name='presaleData[contractAddress]']").closest('.csf-field').css('display', 'none');
    } else if (Tokenico.presaleStatus == 'publish') {
        $(".csf-section input, .csf-section select").attr("disabled", "disabled");
        $(".csf-section .csf--switcher").closest('.csf-fieldset').prepend("<div class='disabled'></div>");
        $("[name='presaleData[contractAddress]']").closest('.csf-field').css('display', 'block');
        $(".important-note-content").css('display', 'none');
    }

    const multiChain = new MultiChain({
        acceptedChains: Tokenico.acceptedChains,
        acceptedWallets: [
            'metamask'
        ]
    });
    
    function infoPopup(message, html = null) {
        return Swal.fire({
            title: message,
            html,
            icon: 'info',
            didOpen: () => {
                Swal.hideLoading();
            }
        });
    }
    
    function errorPopup(message, html = null) {
        return Swal.fire({
            title: message,
            html,
            icon: 'error',
            didOpen: () => {
                Swal.hideLoading();
            }
        });
    }
    
    function successPopup(message, html = null) {
        return Swal.fire({
            title: message,
            html,
            icon: 'success',
            didOpen: () => {
                Swal.hideLoading();
            }
        });
    }
    
    function waitingPopup(title, html = null) {
        Swal.fire({
            title,
            html,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    function disableScreen() {
        var div = document.createElement("div");
        div.className += "overlay";
        div.style.backgroundColor = "#EFEFEF";
        div.style.position = "fixed";
        div.style.width = "100%";
        div.style.height = "100%";
        div.style.zIndex = "999999999999999";
        div.style.top = "0px";
        div.style.left = "0px";
        div.style.opacity = ".5";
        document.body.appendChild(div);
    }

    function ongoingProcess() {
        window.onbeforeunload = () => "There is an ongoing process, please do not close the browser.";
    }
    
    function ongoingProcessEnded() {
        window.onbeforeunload = false;
    }

    function checkFields() {
        return new Promise((resolve, reject) => {
            $.each($(".csf-field input"), (key, element) => {
                let val = $(element).val();
                let id = $(element).attr("data-depend-id");
                if (!val && !willNotBeChecked.includes(id)) {
                    let title = $(element).closest('.csf-field').find('.csf-title h4').html();
                    errorPopup(Tokenico.lang.fieldCannotBeEmpty.replace('%s', title));
                    reject(false);
                    return false;
                }
            });
            resolve(true);
        });
    }

    function networkConfirm(networkName) {
        return new Promise((resolve, reject) => {
            Swal.fire({
                title: Tokenico.lang.confirmNetwork.replace('%s', networkName),
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: Tokenico.lang.confirm,
                cancelButtonText: Tokenico.lang.cancel
            }).then((result) => {
                if (result.isConfirmed) {
                    resolve(true);
                } else {
                    reject(false);
                }
            });
        });
    }

    function connect() {
        return new Promise((resolve, reject) => {
            multiChain.connect('metamask')
            .then(() => {
                multiChain.connector.chainChanged(() => {
                    window.location.reload();
                });

                multiChain.connector.accountsChanged(() => {
                    window.location.reload();
                });

                multiChain.connector.disconnectEvent(() => {
                    window.location.reload();
                });
                resolve(true);
            })
            .catch((error) => {
                if (typeof error === 'object') {
                    if (error.code == 4001) {
                        infoPopup(Tokenico.lang.connectionRefused);
                    }
                } else if (error == 'wallet-not-detected') {
                    infoPopup(Tokenico.lang.metaMaskNotDetected);
                } else if (error == 'not-accepted-wallet') {
                    infoPopup(Tokenico.lang.notAcceptedWallet);
                } else if (error == 'not-accepted-chain') {
                    errorPopup(Tokenico.lang.notAcceptedChains);
                } else {
                    errorPopup(error.message);
                }
                reject(false);
            });
        });
    }

    let published = false;
    async function approveProcess(token, contractAddress, amount) {
        ongoingProcess();
        waitingPopup(Tokenico.lang.saleApprove);
        try {

            let transactionHash = await token.approve(contractAddress, amount);
            let transaction = multiChain.transaction(transactionHash);
            waitingPopup(
                Tokenico.lang.approveProcess, 
                `${Tokenico.lang.transactionId} <a href="${transaction.getTransactionUrl()}" target="_blank">${transaction.getId()}</a>`
            );
            transaction.wait()
            .then(() => {
                successPopup(Tokenico.lang.approveProcessSuccess).then(() => {
                    published = true;
                    disableScreen();
                    $("[name='publish']").click();
                });
            })
            .catch(() => {
                errorPopup(Tokenico.lang.approveProcessFailed);
            })
            .finally(() => {
                ongoingProcessEnded();
            });
        } catch (error) {
            ongoingProcessEnded();
            if (typeof error == 'object') {
                if (error.code == 4001) {
                    infoPopup(Tokenico.lang.requestRejected);
                } else if (error.code == -32603) {
                    infoPopup(Tokenico.lang.intrinsicGasTooLow);
                } else {
                    errorPopup(Tokenico.lang.invalidTokenAddress);
                }
            } else {
                errorPopup(Tokenico.lang.invalidTokenAddress);
            }
        }
    }

    function toTimestamp(startDate, endDate) {
        return new Promise((resolve, reject) => {
            $.ajax({
                method: 'GET',
                url: Tokenico.apiUrl + '/get-dates',
                data: {
                    startDate,
                    endDate
                },
                success(response) {
                    let startDate = response.data.startDate;
                    let endDate = response.data.endDate;
                    resolve({startDate, endDate})
                },
                error() {
                    reject(false);
                },
            });
        });
    }

    $("#post").submit(async function(e) {

        let clickedEl = e.originalEvent.submitter;
        let publishBtn = $("[name='publish']")[0];

        if (clickedEl != publishBtn || published == true) {
            return;
        } 

        e.preventDefault();

        if (await checkFields() === false) {
            return false;
        } 

        let contract = $(".csf-field select[data-depend-id='contract']").val();
        let tokenAddress = $(".csf-field input[data-depend-id='tokenAddress']").val();
        let totalSaleLimit = parseFloat($(".csf-field input[data-depend-id='totalSaleLimit']").val());
        let minContribution = parseFloat($(".csf-field input[data-depend-id='minContribution']").val());
        let maxContribution = parseFloat($(".csf-field input[data-depend-id='maxContribution']").val());
        let exchangeRate = parseInt($(".csf-field input[data-depend-id='exchangeRate']").val());
        let startDate = $(".csf-field input[data-depend-id='startDate']").val();
        let endDate = $(".csf-field input[data-depend-id='endDate']").val();
        let autoTransfer = $(".csf-field input[data-depend-id='autoTransfer']").val() == 1;
        let tokenicol = Tokenico.tokenicol == 'yes' ? true : false;
        let totalTokensBeSold = (totalSaleLimit * exchangeRate);

        $(".csf-field input[data-depend-id='remainingLimit']").val(totalSaleLimit);

        $("[name='presaleData[exchangeRate]']").val(exchangeRate);

        if (!MultiChain.utils.isAddress(tokenAddress)) {
            return errorPopup(Tokenico.lang.invalidContractAddress);
        }

        if (minContribution > totalSaleLimit || maxContribution > totalSaleLimit) {
            return errorPopup(Tokenico.lang.bigMinMaxContribution);
        }
        
        if (minContribution > maxContribution) {
            return errorPopup(Tokenico.lang.bigMinContribution);
        }

        if (startDate > endDate) {
            return errorPopup(Tokenico.lang.bigStartDate);
        }

        if (await connect() === false) {
            return false;
        }

        totalSaleLimit = totalSaleLimit * (10 ** multiChain.activeChain.nativeCurrency.decimals);
        minContribution = minContribution * (10 ** multiChain.activeChain.nativeCurrency.decimals);
        maxContribution = maxContribution * (10 ** multiChain.activeChain.nativeCurrency.decimals);
        
        let token = multiChain.token(tokenAddress);
        let contractAddress = $("[name='presaleData[contractAddress]']").val();
        if (contractAddress) {
            let approvedQuantity = await token.allowance(multiChain.connectedAccount, contractAddress);
            if (approvedQuantity < totalTokensBeSold) {
                approveProcess(token, contractAddress, (totalTokensBeSold - approvedQuantity));
            } else {
                infoPopup(Tokenico.lang.approveProcessSuccess).then(() => {
                    published = true;
                    $("[name='publish']").click();
                });
            }
        } else {

            if (await networkConfirm(multiChain.activeChain.name) === false) {
                return false;
            }

            let result = await toTimestamp(startDate, endDate);
            if (!result) {
                return errorPopup(Tokenico.lang.getDatesError);
            } else {
                startDate = result.startDate;
                endDate = result.endDate;
            }

            waitingPopup(Tokenico.lang.pleaseWait);

            const factory = Tokenico.factories[contract];
            const contractVersion = Tokenico.versions[contract];
            const contractAbi = factory[contractVersion];
            const byteCode = factory.byteCode;
            
            $("[name='presaleData[network]']").val(JSON.stringify(multiChain.activeChain));
            $("[name='presaleData[networkId]']").val(multiChain.activeChain.hexId);
            $("[name='presaleData[contractVersion]']").val(contractVersion);
            $("[name='presaleData[adminAddress]']").val(multiChain.connectedAccount);
            try {
                $("[name='presaleData[token]']").val(JSON.stringify({
                    name: await token.getName(),
                    symbol: await token.getSymbol(),
                    address: await token.getAddress(),
                    decimals: await token.getDecimals(),
                    totalSupply: await token.getTotalSupply(),
                }));
            } catch (err) {
                errorPopup(Tokenico.lang.invalidTokenAddress);
                return false;
            }
            
            let balance = await token.getBalance(multiChain.connectedAccount);
            if (balance < totalTokensBeSold)  {
                ongoingProcessEnded();
                return errorPopup(Tokenico.lang.saleBalanceError);
            }

            let newContract = multiChain.contract(contractAbi);
            
            let data = newContract.new.getData(
                tokenAddress,
                totalSaleLimit,
                minContribution, 
                maxContribution,
                exchangeRate,
                startDate,
                endDate,
                autoTransfer,
                tokenicol,
                {
                    from: multiChain.connectedAccount,
                    data: byteCode
                }
            );

            let gas = await multiChain.getEstimateGas({
                from: multiChain.connectedAccount,
                data: "0x" + data
            });

            ongoingProcess();
            newContract.new(
                tokenAddress,
                totalSaleLimit,
                minContribution, 
                maxContribution,
                exchangeRate,
                startDate,
                endDate,
                autoTransfer,
                tokenicol,
                {
                    from: multiChain.connectedAccount,
                    data: byteCode,
                    gas
                }, async function(err, contract) {
                    ongoingProcessEnded();
                    if(!err) {
                        if(!contract.address) {
                            let transaction = multiChain.transaction(contract.transactionHash);
                            waitingPopup(
                                Tokenico.lang.deployContract, 
                                `${Tokenico.lang.transactionId} <a href="${transaction.getTransactionUrl()}" target="_blank">${transaction.getId()}</a>`
                            );
                        } else {
                            $("[name='presaleData[contractAddress]']").val(contract.address);
                            approveProcess(token, contract.address, totalTokensBeSold);
                        }
                    } else {
                        if (typeof err == 'object') {
                            if (err.code == 4001) {
                                infoPopup(Tokenico.lang.requestRejected);
                            } else if (err.code == -32603) {
                                infoPopup(Tokenico.lang.intrinsicGasTooLow);
                            } else {
                                infoPopup(Tokenico.lang.unexpectedError);
                            }
                        } else {
                            errorPopup(Tokenico.lang.unexpectedError);
                        }
                    }
                }
            );
        }

    });

})(jQuery);