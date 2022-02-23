let menuLinks = document.querySelectorAll(".navbar-nav .nav-link")
menuLinks[2].classList.add("active")



function fetchContact(searchQuery = null) 
{
    args = searchQuery ? {
        'query': searchQuery,
    } : null;
    link = "/contact/feed"
    //console.log(args)
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
                   // console.log(value)
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
    queryValue = document.querySelector('#searchInput input').value;
    //console.log(queryValue)
    fetchContact(queryValue);
})

fetchContact();