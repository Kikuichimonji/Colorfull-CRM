let menuLinks = document.querySelectorAll(".navbar-nav .nav-link")
menuLinks[3].classList.add("active")

let newInput = document.querySelector("#newInput")
let deleteList = document.querySelectorAll(".fa-ban")
let inputGroupsList = document.querySelectorAll(".form-group")
inputGroupsList.forEach(inputGroup => {
    inputGroup.hiddenId = inputGroup.id;
    inputGroup.removeAttribute("id");
})

function minusEl(el)
{

    el.querySelector("span").innerText = el.hiddenId <10 ? "0" + (el.hiddenId -1) : (el.hiddenId -1);
    let labelInput = el.querySelector("#label_"+el.hiddenId)
    labelInput.setAttribute("name","label_"+(el.hiddenId-1))
    labelInput.setAttribute("id","label_"+(el.hiddenId-1))
    let labelLabel = labelInput.nextElementSibling;
    labelLabel.setAttribute("for","label_"+(el.hiddenId-1))
    let selectInput = el.querySelector("#inputType_"+el.hiddenId)
    selectInput.setAttribute("name","inputType_"+(el.hiddenId-1))
    selectInput.setAttribute("id","inputType_"+(el.hiddenId-1))
    let selectLabel = selectInput.nextElementSibling;
    selectLabel.setAttribute("for","inputType_"+(el.hiddenId-1))
    let radioButtons = el.querySelectorAll(".radioCompany input")
    radioButtons[0].setAttribute("name","forCompany_"+(el.hiddenId-1))
    radioButtons[0].setAttribute("id","company_"+(el.hiddenId-1))
    radioButtons[1].setAttribute("name","forCompany_"+(el.hiddenId-1))
    radioButtons[1].setAttribute("id","person_"+(el.hiddenId-1))
    el.hiddenId = el.hiddenId-1
    if(el.nextElementSibling){
        nextEl = el.nextElementSibling
        minusEl(nextEl)
    }
}

deleteList.forEach(icon => {
    icon.addEventListener("click", ev => {
        
        ev.target.parentNode.nextElementSibling ? minusEl(ev.target.parentNode.nextElementSibling) : null
        ev.target.parentNode.outerHTML = "";
    })
});

newInput.addEventListener("click", ev => {
    ev.preventDefault();
    let countInput = document.querySelectorAll("#formTab1 .form-group").length + 1
    let containerDiv = document.createElement("DIV");
    let span = document.createElement("SPAN");
    let labelDiv = document.createElement("DIV");
    let labelInput = document.createElement("INPUT");
    let labelLabel = document.createElement("LABEL");
    let typeDiv = document.createElement("DIV");
    let typeSelect = document.createElement("SELECT");
    let typeLabel = document.createElement("LABEL");
    let radioField = document.createElement("FIELDSET");
    let radioCompanyDiv = document.createElement("DIV");
    let radioPersonDiv = document.createElement("DIV");
    let radioCompanyLabel = document.createElement("LABEL");
    let radioPersonLabel = document.createElement("LABEL");
    let radioCompanyInput = document.createElement("INPUT");
    let radioPersonInput = document.createElement("INPUT");
    let deleteIcon = document.createElement("I");

    containerDiv.classList.add("form-group");
    containerDiv.hiddenId = countInput;
    span.innerText = countInput < 10 ? "0" + countInput: countInput;

    labelDiv.className = "form-floating fieldsSize";
    labelInput.setAttribute("type","text")
    labelInput.setAttribute("name","label_" + countInput)
    labelInput.setAttribute("id","label_" + countInput)
    labelInput.setAttribute("placeholder","label")
    labelInput.classList.add('form-control')
    labelInput.required = true;
    labelLabel.setAttribute("for","label_" + countInput)
    labelLabel.innerText = "Label"

    typeDiv.className = "input-group fieldsSize";
    typeSelect.classList.add('form-select')
    typeSelect.setAttribute("name","inputType_" + countInput)
    typeSelect.setAttribute("id","inputType_" + countInput)
    let typeArray = ['text','textarea','color']
    typeArray.forEach(type => {
        let option = document.createElement("OPTION");
        option.innerText = type
        typeSelect.appendChild(option)
    });
    typeLabel.setAttribute("for","inputType_" + countInput)
    typeLabel.classList.add('input-group-text')
    typeLabel.innerText = "Input Type"

    radioField.className = "radioCompany fieldsSize";
    radioCompanyDiv.classList.add('form-check');
    radioPersonDiv.classList.add('form-check');
    radioCompanyLabel.classList.add("form-check-label");
    radioCompanyLabel.innerText = "Société"
    radioPersonLabel.classList.add("form-check-label");
    radioPersonLabel.innerText = "Personne"
    radioCompanyInput.setAttribute("type","radio");
    radioPersonInput.setAttribute("type","radio");
    radioCompanyInput.className = "form-check-input ms-1 me-1";
    radioPersonInput.className = "form-check-input ms-2 me-1";
    radioCompanyInput.setAttribute("name","forCompany_" + countInput);
    radioPersonInput.setAttribute("name","forCompany_" + countInput);
    radioCompanyInput.setAttribute("id","company_" + countInput);
    radioPersonInput.setAttribute("id","person_" + countInput);
    radioCompanyInput.value = "company"
    radioPersonInput.value = "person";
    radioCompanyInput.required=true;

    deleteIcon.className = "fas fa-ban"
    deleteIcon.addEventListener("click", ev => {
        
        ev.target.parentNode.nextElementSibling ? minusEl(ev.target.parentNode.nextElementSibling) : null
        ev.target.parentNode.outerHTML = "";
    })

    containerDiv.appendChild(span);
    containerDiv.appendChild(labelDiv);
    labelDiv.appendChild(labelInput);
    labelDiv.appendChild(labelLabel);
    containerDiv.appendChild(typeDiv);
    typeDiv.appendChild(typeSelect);
    typeDiv.appendChild(typeLabel);
    containerDiv.appendChild(radioField);
    radioField.appendChild(radioCompanyDiv);
    radioCompanyDiv.appendChild(radioCompanyLabel);
    radioCompanyLabel.appendChild(radioCompanyInput);
    radioField.appendChild(radioPersonDiv);
    radioPersonDiv.appendChild(radioPersonLabel);
    radioPersonLabel.appendChild(radioPersonInput);
    containerDiv.appendChild(deleteIcon);
    document.querySelector("#formTab1 div").appendChild(containerDiv);
})