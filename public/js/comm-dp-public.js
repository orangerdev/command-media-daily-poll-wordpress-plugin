(function( $ ) {
	'use strict';

	$(document).on('submit','.comm-dp-poll-anwser form',function(){
		let vote = $('.comm-dp-poll-anwser form input[name=answer]:checked').val();
		let key = $('.comm-dp-poll-anwser form input[name=commdp-nonce]').val();
		let poll_id = $('.comm-dp-poll-anwser form input[name=poll_id]').val();

		if('undefined' === typeof vote) {
			alert(commdp.message.noVote);
		} else {
			$.ajax({
				url : commdp.url.pollSubmit,
				method : 'POST',
				dataType : 'json',
				data : {
					'poll_id' : poll_id,
					'answer' : vote,
					'commdp-nonce' : key
				},
				beforeSend : function() {
					$('.comm-dp-poll-anwser form button').attr('disabled',true).html('Send your vote...');
				},
				success : function(response) {
					console.log(response);
				}
			})
		}
		return false;
	});

})( jQuery );
