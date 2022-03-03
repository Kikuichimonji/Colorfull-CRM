let menuLinks = document.querySelectorAll(".navbar-nav .nav-link")
menuLinks[2].classList.add("active")
let extrafieldsCompany = document.getElementById("extrafieldsCompany")
let extrafieldsPerson = document.getElementById("extrafieldsPerson")
let extrafieldsContainerCompany = document.getElementById("extrafieldsContainerCompany")
let extrafieldsContainerPerson = document.getElementById("extrafieldsContainerPerson")
let CPP = document.querySelector('#nbContactsPerPage');
extrafieldsCompany.style.display = "none"
extrafieldsCompany.nextElementSibling.style.display ="none"
extrafieldsContainerCompany.style.display = "none"
extrafieldsContainerCompany.active = false;
extrafieldsPerson.style.display = "none"
extrafieldsPerson.nextElementSibling.style.display ="none"
extrafieldsContainerPerson.style.display = "none"
extrafieldsContainerPerson.active = false;

/**
 * Verify the filter's checkboxes values and the query value then sed the lik to the fetch method
 *
 * @param mixed incArgs
 * 
 * @return null
 * 
 */
function fetchContact(incArgs = null) 
{
    checkboxes = document.querySelectorAll("#searchInput input[type=checkbox]")

    let args = {};
    incArgs ? args['query'] = incArgs.queryValue : null;

    link = "/contact/feed"; //where we looking for contacts list

    checkboxes.forEach(checkbox => {
        if(checkbox.checked){
            args[checkbox.id] = checkbox.id;
        }
    });
    goFetch(args,link);
}

/**
 * Receive stringged JSON feed in an array, then treat it and create the contact table
 *
 * @param array contacts
 * 
 * @return void
 * 
 */
function contactFeed(contacts,page = 1,newFeed = null)
{
    let body = document.querySelector('#tableContact tbody')
    let contactsPerPage = CPP.value == 'All' ?  999999 : CPP.value;
    let nbPage = Math.ceil(contacts.length/contactsPerPage);
    body.innerHTML = "";
    
    cutContact = contacts
    cutContact = cutContact.slice((contactsPerPage * page)-contactsPerPage,contactsPerPage * page)

    cutContact.forEach(contact => {

        contact = JSON.parse(contact)
        let tr = document.createElement("TR");
        tr.hiddenId = contact.id;

        tr.addEventListener("click", ev => {
            window.location.href = '/contact/' + ev.currentTarget.hiddenId
        })
        let tdColor = document.createElement("TD");
        let tdName = document.createElement("TD");
        let tdPhone = document.createElement("TD");
        let tdEmail = document.createElement("TD");
        let tdCompany = document.createElement("TD");
        tdColor.classList.add('contactColorBlock'); //the colored squares
        let colorCount = 1;

        contact.contactType.forEach(ct => {
            div = document.createElement("DIV");
            div.classList.add("color" + colorCount);
            div.style.backgroundColor = ct.color
            colorCount++;
            tdColor.appendChild(div)
        });

        tdName.textContent = contact.name 
        tdPhone.textContent = contact.phone1 ?? contact.phone2 ?? "/";
        tdEmail.textContent = contact.email;
        tdCompany.textContent = contact.isCompany ? "Société" : "Particulier";
        body.appendChild(tr);
        tr.appendChild(tdColor);
        tr.appendChild(tdName);
        tr.appendChild(tdPhone);
        tr.appendChild(tdEmail);
        tr.appendChild(tdCompany);

    });

    let pagination = document.querySelector(".pagination")
    pagination.innerHTML = ""
    for (let count = 1; count <= nbPage; count++) {
        
        let li = document.createElement("LI");
        let link = document.createElement("A");
        pagination.appendChild(li)
        li.appendChild(link)
        li.classList.add("page-item")
        page == count ? li.classList.add("active") : null;
        link.classList.add("page-link")
        link.innerHTML = count
        link.hiddenId = count
        link.addEventListener("click", ev => {
            ev.target.scrollIntoView({inline: "center",behavior: "smooth"});
            contactFeed(contacts,ev.target.hiddenId)
        })
    }

    function contactsPerPageHandler(ev) 
    {

        ev.stopImmediatePropagation();
        ev.target.removeEventListener("change",contactsPerPageHandler)
        contactFeed(contacts,1,1)
    }
    CPP.addEventListener("change", contactsPerPageHandler)

    if(newFeed){
        //console.log(document.querySelector('#contactTab').clientHeight)
    }
}

/**
 * Get the destination and the arguments then send them via fetch, then return the json response from the destination 
 *
 * @param json args
 * @param string link
 * 
 * @return Json|string
 * 
 */
function goFetch(args, link)
{
    let myHeaders = new Headers(); //If we want custom headers
    myHeaders = {

    };
    let formData = new FormData(); //We append the POST data here

    for (const key in args) { //Putting all the args in the POST body
        formData.append(key, args[key])
    }
    let myInit = {
        method: 'POST',
        headers: myHeaders,
        mode: 'cors',
        cache: 'default',
        body: formData
    };
    const element = document.querySelector('#get-request .result');
    let myRequest = new Request(link, myInit); //building the request with all the parameters
    fetch(myRequest) //starting the fetch request
        .then((response) => {
            const isJson = response.headers.get('content-type')?.includes('application/json');
            const data = isJson ? response.json() : null;
            const xError = response.headers.get('X-debug-Exception');
            xError ? console.log(decodeURI(xError)) : null;

            if (!response.ok) { //showing the error 
                // get error message from body or default to response status
                const error = (data && data.message) || response.status;
                return Promise.reject(error);
            } else {
                data.then(value => { 
                    //console.log(value)
                    contactFeed(value)
                })
            }
        })
        .catch(error => {
            console.log('There was an error!', error);
        })
}

/**
 * Launch when we click on the contact search button then fetch the events
 */
document.querySelector('#searchInput button').addEventListener("click", ev => {
    ev.preventDefault();
    queryValue = document.querySelector('#searchInput #search').value;
    args = {
        "queryValue": queryValue,
        }
    fetchContact(args);
})

/**
 * Launch when we check the phone then fetch the contacts
 */
document.getElementById("checkPhones").addEventListener("change", ev => {
    fetchContact();
});
/**
 * Launch when we check the email then fetch the contacts
 */
document.getElementById("checkEmails").addEventListener("change", ev => {
    fetchContact();
});
/**
 * Launch when we check the company then fetch the contacts
 */
document.getElementById("checkisCompany").addEventListener("change", ev => {
    if(document.getElementById("checkCompany").checked == true){
        fetchContact();
    }
});
/**
 * Launch when we switch the company status then fetch the contacts
 */
document.getElementById("checkCompany").addEventListener("change", ev => {
    companySwitch = document.getElementById("checkisCompany");
    companyLabel = document.getElementById("isCompanyLabel");
    companySwitch.style.display = companySwitch.style.display == "block" ? "none" : "block";
    companyLabel.style.display = companySwitch.style.display == "block" ? "block" : "none";
    
    fetchContact();
});
/**
 * Launch when we clear the search box with the keyboard then fetch the contacts
 */
document.getElementById("search").addEventListener("keyup", ev => {
    if(ev.target.value == ""){
        fetchContact();
    }
});
/**
 * Launch when we clear the search box with the clear button in the box then fetch the contacts
 */
document.getElementById("search").addEventListener("search", ev => { //Trigger when we click on the clear search button
    fetchContact();
});
fetchContact();

/**
 * hide and show different elements when we switch the company status
 */
document.getElementById("company").addEventListener("change", ev => {
    extrafieldsCompany.style.display = "block"
    extrafieldsCompany.nextElementSibling.style.display ="block"
    extrafieldsContainerCompany.style.display = "block"
    extrafieldsContainerCompany.active = true
    extrafieldsPerson.style.display = "none"
    extrafieldsPerson.nextElementSibling.style.display ="none"
    extrafieldsContainerPerson.style.display = "none"
    extrafieldsContainerPerson.active = false
});
document.getElementById("person").addEventListener("change", ev => {
    extrafieldsCompany.style.display = "none"
    extrafieldsCompany.nextElementSibling.style.display ="none"
    extrafieldsContainerCompany.style.display = "none"
    extrafieldsContainerCompany.active = false
    extrafieldsPerson.style.display = "block"
    extrafieldsPerson.nextElementSibling.style.display ="block"
    extrafieldsContainerPerson.style.display = "block"
    extrafieldsContainerPerson.active = true
});


/**
 * Use the mutator observer for the classList to hide the filter/search input groups
 */
function hideSearchInputs(mutationsList) {
    mutationsList.forEach(mutation => {
        if (mutation.attributeName === 'class') {
            document.querySelector("#contactTab").classList.contains("active") ? document.querySelector("#searchInput").classList.add("show") : document.querySelector("#searchInput").classList.remove("show")
        }
    })
}

const mutationObserver = new MutationObserver(hideSearchInputs)

mutationObserver.observe( //will oberve the attribute list of the element
    document.getElementById('contactTab'),
    { attributes: true }
)


document.getElementById("extrafieldsCompanyButton").addEventListener("click", ev => { //we create the extrafields when we click on the button
    let selectBox = ev.target.previousElementSibling;
    let el = selectBox.options[selectBox.selectedIndex];
    let field = document.getElementById("addNewContact").querySelector("#extrafieldsContainerCompany");
    let div = document.createElement("DIV");


    if(el.getAttribute("datatype") != "textarea"){
        extrafield = document.createElement("INPUT");
        extrafield.setAttribute("type",el.getAttribute("datatype"))
    }else{
        extrafield = document.createElement("TEXTAREA");
    }
    div.classList.add("form-group")
    div.classList.add("mt-2")
    extrafield.classList.add("form-control");
    extrafieldLabel = document.createElement("LABEL");
    extrafield.setAttribute("name","EX_" + el.getAttribute("dataid") + "_"+ el.textContent)
    extrafieldLabel.textContent = el.textContent;
    extrafieldLabel.classList.add("form-label");
    selectBox.remove(selectBox.selectedIndex);
    if(selectBox.options.length == 0){
        selectBox.parentNode.style.display = "none";
    }

    div.appendChild(extrafieldLabel)
    div.appendChild(extrafield)
    field.appendChild(div)

})

