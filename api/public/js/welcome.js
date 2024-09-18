(function() {
  'use strict';
  var gallery = document.querySelector('div[data-gallery]');
  var pagination = document.querySelector('nav[data-pagination]');
  var page = 1, itemsPerPage = 9;

  fetchData(page, itemsPerPage);

  function renderGallery(data) {
    gallery.innerHTML = '';
    data.map((chunk) => {
      var container = document.createElement('div');
      container.className = 'col-lg-4 col-md-12 mb-4 mb-lg-0';
      chunk.map((element) => container.appendChild(createGallery(element)))
      gallery.appendChild(container);
    })
  }

  function renderPagination(page, total) {
    const totalPages = Math.ceil(total / itemsPerPage);
    const ul = document.createElement('ul');

    ul.className = 'pagination justify-content-center';

    function createPageItem(text, disabled, active, onClick) {
      const li = document.createElement('li');
      li.className = 'page-item';
      if (disabled) li.classList.add('disabled');
      if (active) li.classList.add('active');

      const a = document.createElement('a');
      a.className = 'page-link';
      a.href = '#';
      a.innerText = text;
      if (onClick) a.onclick = function (event) {
        event.preventDefault();
        if (!active) {
          onClick();
          gallery.scrollIntoView(true);
          window.scrollBy(0, -10);
        }
      };

      li.appendChild(a);
      return li;
    }

    // Previous
    ul.appendChild(
      createPageItem('Previous', page <= 1, false, page <= 1 ? null : function () {
        return fetchData(page - 1, itemsPerPage);
      })
    );

    // Pages
    for (let i = 1; i <= totalPages; i++) {
      ul.appendChild(
        createPageItem(i, false, i === page, function () {
          return fetchData(i, itemsPerPage);
        })
      );
    }

    // Next
    ul.appendChild(
      createPageItem('Next', page >= totalPages, false, page >= totalPages ? null : function () {
        return fetchData(page + 1, itemsPerPage);
      })
    );

    pagination.innerHTML = '';
    pagination.appendChild(ul);
  }

  function fetchData(page, itemsPerPage) {
    spinner(gallery)
    httpClient
      .get(`gallery?chunk=3&page=${page}&items_per_page=${itemsPerPage}`)
      .then((response) => {
        renderGallery(response.data);
        renderPagination(page, response.total);
      })
      .catch((error) => notify(error.message, 'error'))
      .finally(() => spinner(gallery))
  }
})()