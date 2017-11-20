function checkID(formElement)
                {
                         re = /^[A-Za-z]\d{9}$/;
                         if (!re.test(formElement.value))
                        alert("i111111111你的帳號只能是數字英文字母及「_」「-」等，不能輸入其他字完！");

                }

