let menuLinks = document.querySelectorAll(".navbar-nav .nav-link")
menuLinks[2].classList.add("active")
let extrafieldsCompany = document.getElementById("extrafieldsCompany")
let extrafieldsPerson = document.getElementById("extrafieldsPerson")
extrafieldsCompany.style.display = "none"
extrafieldsCompany.nextElementSibling.style.display ="none"
extrafieldsPerson.style.display = "none"
extrafieldsPerson.nextElementSibling.style.display ="none"


function fetchContact(incArgs = null) 
{
    checkboxes = document.querySelectorAll("#searchInput input[type=checkbox]")

    let args = {};
    incArgs ? args['query'] = incArgs.queryValue : null;

    link = "/contact/feed";

    checkboxes.forEach(checkbox => {
        if(checkbox.checked){
            args[checkbox.id] = checkbox.id;
        }
    });
    goFetch(args,link);
}

function contactFeed(contacts)
{
    let body = document.querySelector('#tableContact tbody')
    body.innerHTML = "";

    contacts.forEach(contact => {

        contact = JSON.parse(contact)
        let tr = document.createElement("TR");
        let tdColor = document.createElement("TD");
        let tdName = document.createElement("TD");
        let tdPhone = document.createElement("TD");
        let tdEmail = document.createElement("TD");
        let tdCompany = document.createElement("TD");
        tdColor.classList.add('contactColorBlock');
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
    
}

function goFetch(args, link)
{
    let myHeaders = new Headers(); //If we want custom headers
    myHeaders = {

    };
    let formData = new FormData(); //We append the POST data here

    for (const key in args) {
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
    let myRequest = new Request(link, myInit);
    fetch(myRequest)
        .then((response) => {
            const isJson = response.headers.get('content-type')?.includes('application/json');
            const data = isJson ? response.json() : null;
            const xError = response.headers.get('X-debug-Exception');
            xError ? console.log(decodeURI(xError)) : null;

            if (!response.ok) {
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

document.querySelector('#searchInput button').addEventListener("click", ev => {
    ev.preventDefault();
    queryValue = document.querySelector('#searchInput #search').value;
    args = {
        "queryValue": queryValue,
        }
    fetchContact(args);
})

document.getElementById("checkPhones").addEventListener("change", ev => {
    fetchContact();
});
document.getElementById("checkEmails").addEventListener("change", ev => {
    fetchContact();
});
document.getElementById("checkisCompany").addEventListener("change", ev => {
    if(document.getElementById("checkCompany").checked == true){
        fetchContact();
    }
});
document.getElementById("checkCompany").addEventListener("change", ev => {
    companySwitch = document.getElementById("checkisCompany");
    companyLabel = document.getElementById("isCompanyLabel");
    companySwitch.style.display = companySwitch.style.display == "block" ? "none" : "block";
    companyLabel.style.display = companySwitch.style.display == "block" ? "block" : "none";
    
    fetchContact();
});

document.getElementById("search").addEventListener("keyup", ev => {
    if(ev.target.value == ""){
        fetchContact();
    }
});
document.getElementById("search").addEventListener("search", ev => { //Trigger when we click on the clear search button
    fetchContact();
});
fetchContact();

document.getElementById("company").addEventListener("change", ev => {

    extrafieldsCompany.style.display = "block"
    extrafieldsCompany.nextElementSibling.style.display ="block"
    extrafieldsPerson.style.display = "none"
    extrafieldsPerson.nextElementSibling.style.display ="none"
});
document.getElementById("person").addEventListener("change", ev => {
    extrafieldsCompany.style.display = "none"
    extrafieldsCompany.nextElementSibling.style.display ="none"
    extrafieldsPerson.style.display = "block"
    extrafieldsPerson.nextElementSibling.style.display ="block"
});

document.querySelector("#contactTab").addEventListener("click", ev =>{
    if(ev.target.classList.contains("active")){
        
    }
})

function callback(mutationsList) {
    mutationsList.forEach(mutation => {
        if (mutation.attributeName === 'class') {
            document.querySelector("#contactTab").classList.contains("active") ? document.querySelector("#searchInput").classList.add("show") : document.querySelector("#searchInput").classList.remove("show")
        }
    })
}

const mutationObserver = new MutationObserver(callback)

mutationObserver.observe(
    document.getElementById('contactTab'),
    { attributes: true }
)