let menuLinks = document.querySelectorAll(".navbar-nav .nav-link")
menuLinks[0].classList.add("active")
let contacts = document.querySelectorAll(".contactLine")

contacts.forEach(contact => {
    contact.hiddenId = contact.id;
    contact.removeAttribute("id");
    contact.addEventListener("click", ev => {
        window.location.href = '/contact/' + ev.currentTarget.hiddenId
    })
})