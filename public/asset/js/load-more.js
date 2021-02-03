$(document).ready(function () {

    // Load more data
    $('.load-more').click(function () {
        event.preventDefault();
        const baseurl = this.href;
        const icon = this.querySelector('i');

        var page = Number($('#page').val());
        var pagecount = Number($('#pagecount').val());

        if (page < pagecount) {
            var page = page + 1;
            $.ajax({
                url: baseurl,
                type: 'post',
                data: { page: page },
                beforeSend: function () {
                    icon.classList.replace('fa-plus-square', 'fa-spinner')
                },
                success: function (response) {

                    // Setting little delay while displaying new content
                    setTimeout(function () {
                        // appending posts after last post with class="post"
                        $(".trick-item:last").after(response).show().fadeIn("slow");

                        // checking row value is greater than allcount or not
                        if (page >= pagecount) {
                            $('.load-more').hide();
                        } else {
                            icon.classList.replace('fa-spinner', 'fa-plus-square')
                        }
                    }, 1);
                }
            });
            $("#page").val(page);
        }
    });
});