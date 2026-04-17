import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import "//code.jquery.com/jquery-4.0.0.min.js";

$(document).on('click', '.add-warrior-btn', function() {
    var newWarriorInput = $('div.warrior-inputs').first().clone();
    newWarriorInput.find('input').val('');
    $(this).before(newWarriorInput);
});

$(document).on('click', '.db-remove-warrior', function() {
    if (confirm('Are you sure you want to remove this warrior?')) {
        // var deleteUrl = $(this).attr('href');
        // $.ajax({
        //     url: deleteUrl,
        //     type: 'DELETE',
        //     data: {
        //         _token: $('meta[name="csrf-token"]').attr('content')
        //     },
        //     success: function(response) {
        //         alert(response.message);
        //         location.reload();
        //     },
        //     error: function(xhr) {
        //         alert('Error removing warrior.');
        //     }
        // });
        return true; // allow default link behavior for now
    }
});

$(document).on('click', '.remove-warrior-btn', function() {
    if ($('div.warrior-inputs').length > 1) {
        $(this).closest('div.warrior-inputs').remove();
    }
});

$(document).on('click', '#addStoryButton', function() {
    var newStoryInput = $('div.story-inputs-hidden').clone();
    newStoryInput.removeClass('story-inputs-hidden hidden');
    newStoryInput.addClass('story-inputs');
    $('form.add-stories-form input').last().after(newStoryInput);
    newStoryInput.find('textarea').focus();
});

$(document).on('click', '.remove-story-input', function(){
    $(this).parent().remove();
});


/************************ Are you sure you want to vote popup */
if (document.getElementById('popup-modal')){
    // #todo this can probably be done better
    // set the modal menu element
    const $targetEl = document.getElementById('popup-modal');

    // options with default values
    // backdrop: 'dynamic',
    // const options = {
    //     placement: 'bottom-right',
    //     backdropClasses:
    //         'bg-stone-900/50 dark:bg-stone-900/80 fixed inset-0 z-40',
    //     closable: true,
    //     onHide: () => {
    //         console.log('modal is hidden');
    //     },
    //     onShow: () => {
    //         console.log('modal is shown');
    //     },
    //     onToggle: () => {
    //         console.log('modal has been toggled');
    //     },
    // };

    // instance options object
    // const instanceOptions = {
    //   id: 'modalEl',
    //   override: true
    // };

    /*
    * $targetEl: required
    * options: optional
    */
    const modal = new Modal($targetEl);

    $(document).on('click', '.vote-button', function(e){
        e.preventDefault();
        // var name = $(this).text().trim();
        var name = $(this).attr('data-vote-name');
        // var url = $(this).attr('data-vote-url');
        var id = $(this).attr('data-vote-id');
        $('#confirm-vote-btn').attr('data-vote-id', id);
        // $('#confirm-vote-btn').attr('href', url);
        $('.vote-name').text(name);
        modal.show();
        return false;
    })

    $(document).on('click', '#confirm-vote-btn', function(){
        var id = $(this).attr('data-vote-id');
        if($('#points').length){ 
            console.log($('#points'));
            var maxWage = parseInt($('.maxWage').text());
            var wager = parseInt($('#points').val());
            if(isNaN(wager) || wager < 0 || wager > maxWage){
                alert('Please enter a valid wager between 0 and ' + maxWage);
                $('#points').val(maxWage);
                return false;
            }
            $('input[name="wager"]').val($('#points').val());
        }
        $('#form-' + id).submit();
        return false;
    })
}
