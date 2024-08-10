async function getForm(url, id) {
    let forme = null;
    await fetch(url).then(e => e.text()).then(e => {
        forme = e;
        let baba = new DOMParser().parseFromString(forme, 'text/html').getElementById(id);
        document.querySelector('body').append(baba);
    });
    return forme;
}

function removeForm(id) {
    document.getElementById(id).remove();
}

function getActive() {
    let ans = document.getElementById('buttonEspece').querySelector('.active').getAttribute('data-espece-id');
    return ans == 0 ? '' : ans;
}