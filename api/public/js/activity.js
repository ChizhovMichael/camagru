(function() {
  'use strict';
  var gallery = document.querySelector('div[data-gallery]');
  var page = 1, itemsPerPage = 3, isLoading = false, totalImages = 0, loadedImages = 0;

  fetchData(page, itemsPerPage);

  function fetchData(page, itemsPerPage) {
    if (isLoading) return;
    isLoading = true;

    spinner(gallery)
    httpClient
      .get(`gallery?page=${page}&items_per_page=${itemsPerPage}&user_id=${auth}`)
      .then((response) => {
        var data = response.data;
        totalImages = response.total;

        data.map((element) => {
          var col = document.createElement('div');
          col.className = 'col-12';
          col.appendChild(createGallery(element));
          gallery.appendChild(col);
        })

        loadedImages += data.length;
      })
      .catch((error) => notify(error.message, 'error'))
      .finally(() => {
        isLoading = false;
        spinner(gallery);

        if (loadedImages >= totalImages) {
          window.removeEventListener('scroll', onScroll);
        }
      })
  }

  function onScroll() {
    if (window.innerHeight + window.scrollY >= document.body.scrollHeight - 200) {
      if (loadedImages < totalImages) {
        page++;
        fetchData(page, itemsPerPage);
      }
    }
  }

  window.addEventListener('scroll', onScroll);
})()
