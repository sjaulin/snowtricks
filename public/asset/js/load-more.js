document.addEventListener("DOMContentLoaded", function () {

    var link = document.querySelector('.load-more');
    link.style.display = "block";

    // Load more data
    link.addEventListener('click', function (link) {
        event.preventDefault();
        const baseurl = this.href;
        const icon = this.querySelector('i');

        var npage = Number(document.getElementById("npage").value);
        var pagecount = Number(document.getElementById("pagecount").value);

        if (npage < pagecount) {
            var npage = npage + 1;
            jQuery.ajax({
                url: baseurl,
                type: 'post',
                data: { npage: npage },
                beforeSend: function () {
                    icon.classList.replace('fa-plus-square', 'fa-spinner')
                },
                success: function (response) {

                    // Setting little delay while displaying new content
                    setTimeout(function () {
                        // appending posts after last post with class="post"
                        jQuery(".load-more-item:last").after(response).show().fadeIn("slow");

                        // checking row value is greater than allcount or not
                        if (npage >= pagecount) {
                            jQuery('.load-more').hide();
                        } else {
                            icon.classList.replace('fa-spinner', 'fa-plus-square')
                        }
                    }, 1);
                }
            });
            jQuery("#npage").val(npage);
        }
    });
});

