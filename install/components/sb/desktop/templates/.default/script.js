BX.ready(function () {

})

function sb_readAllComments (userId, groupId) {
    BX.ajax.runAction('tasks.task.comment.readAll', {data: {
        groupId: groupId,
        userId: userId
    }});

    const commentSpan = document.querySelector('#sb-new_comments')

    if (commentSpan === null) {
        return;
    }

    commentSpan.textContent = 0
}