function set_form_api(id_form, action) {
  const f = document.getElementById(id_form);

  if (f == null) return;

  f.addEventListener('submit', e => {
    if (e.target == null) return;
    e.preventDefault();

    const formData = new FormData(f);
    const post = new URLSearchParams();

    formData.forEach((v, k) => {
      post.append(k, v.toString());
    });
    let form_url = f.getAttribute('action') ?? location.href;

    fetch(form_url, {
      "method": f.getAttribute('method'),
      "body": formData
    }).then(e => e.json())
      .then(response => {

        let response_type = response['type'];

        if (response['type'] == "success") {
          response['type'] = "Success";
        } else if (response['type'] == "error") {
          response['type'] = "Erreur";
        }

        let notif = getNotifHtml().querySelector('div');
        if (notif != null) {
          notif.setAttribute('data-notif-id', response['notif_id']);
          let notif_type;

          if ((notif_type = notif.querySelector('.notif_type')) != null) {
            notif_type.classList.add(response_type);
            notif_type.innerHTML = response['type'];
          }

          let notif_resp_type = notif.querySelector('span');
          if (notif_resp_type != null)
            notif_resp_type.classList.add(response_type);

          let notif_message = notif.querySelector('.notif_message');
          if (notif_message != null)
            notif_message.innerHTML = response['message'];
        }

        action();
        reloadEspece();
        reloadElement("animaux");

        const notif_box = document.getElementById('notification-box');
        notif_box.append(notif);

        setTimeout(() => {
          notif.classList.add('notifNone');
          notif.remove();
        }, 5000);
      });

  });
}


function getNotifHtml() {
  return new DOMParser().parseFromString('<div class="notification" data-notif-id=""> \
            <span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> \
            <div class="desrciptNotif"> \
                <h1 class="notif_type" class="">success</h1> \
                <p class="notif_message">L\'histoire a bien commence</p> \
            </div> \
          </div>', 'text/html');
}

function reloadEspece() {
  fetch(location.href).then(e => e.text()).then(response => {
    document.getElementById('buttonEspece').innerHTML = new DOMParser().parseFromString(response, 'text/html').querySelector('#buttonEspece').innerHTML;
  });
}

function reloadElement(id) {
  fetch(location.href).then(e => e.text()).then(response => {
    document.getElementById(id).innerHTML = new DOMParser().parseFromString(response, 'text/html').querySelector('#' + id).innerHTML;
  });
}

function reloadFromUrl(id, url) {
  fetch(url).then(e => e.text()).then(response => {
    document.getElementById(id).innerHTML = new DOMParser().parseFromString(response, 'text/html').querySelector('#' + id).innerHTML;
  });
}

function reloadFromUrl2(id, url) {
  let result;
  fetch(url).then(e => e.text()).then(response => {
    result = new DOMParser().parseFromString(response, 'text/html').querySelector('#' + id).innerHTML;
  });
  return result;
}

function urlParam(formData) {
  const url = new URLSearchParams();
  formData.forEach((v, k) => {
    url.append(k, v);
  });
  return url;
}