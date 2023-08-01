const createProjectBtns = document.querySelectorAll('.create-project__JS');
const createTaskBtns = document.querySelectorAll('.create-task__JS');
const deleteProjectBtns = document.querySelectorAll('.delete-project__JS');
const deleteTaskBtns = document.querySelectorAll('.delete1-project__JS');
const updateProjectBtns = document.querySelectorAll('.update-project__JS');
const updateTaskBtns = document.querySelectorAll('.update1-project__JS');
const cancelBtns = document.querySelectorAll('.pop-up__cancel-btn');
const textarea = document.querySelector('.pop-up__textarea');
const togglerMenu = document.querySelectorAll('.left-menu__btn');
const description = document.querySelectorAll('.project-description__JS');
const descriptionTasks = document.querySelectorAll('.project-description-tasks__JS');
const updateDashboard = document.querySelectorAll('.update-dashboard__JS');
const dragItem = document.querySelectorAll('.drag-item');
const dragArea = document.querySelectorAll('.task-board');
let draggableTodo = null;

function dragStart() {
    draggableTodo = this;
}

const dragEnd = () => {
    draggableTodo = null;
}

function dragOver(e) {
    e.preventDefault();

}

function dragDrop() {
    this.appendChild(draggableTodo);
    const currentStatus = draggableTodo.getAttribute('data-status');
    const task = {
        id: draggableTodo.getAttribute('data-id'),
        title: draggableTodo.textContent,
        description: draggableTodo.getAttribute('data-description'),
        status: draggableTodo.parentElement.children[0].textContent,
        priority: draggableTodo.getAttribute('data-priority'),

    }
    sendSqlQuery(task);
}

dragItem.forEach(item => {
    item.addEventListener('dragstart', dragStart);
    item.addEventListener('dragend', dragEnd);
});

dragArea.forEach(area => {
    area.addEventListener("dragover", dragOver);
    area.addEventListener("drop", dragDrop);
});

const handleProjectCreateForm = () => {
    const form = document.querySelector('.pop-up');
    form.classList.add('pop-up__JS');
    renderBlur();
}


const handleTaskCreateForm = () => {
    const form = document.querySelector('.pop-up');
    form.classList.add('pop-up__JS');
    renderBlur();
}

const renderBlur = () => {
    const blur = document.createElement('div');
    blur.classList.add('blur__JS');
    document.body.appendChild(blur);
}

const removeBlur = () => {
    const blur = document.querySelector('.blur__JS');
    blur.parentNode.removeChild(blur);
}

const handleProjectDeleteForm = (id, title) => {
    const form = document.querySelector('.pop-up__delete');
    const deleteBtn = document.querySelector('.pop-up__confirm-btn');
    const link = `delete.php?Projekto_id=${id}&title=${title}`;
    deleteBtn.setAttribute('href', `${link}`);
    form.classList.add('pop-up__JS');
    renderBlur();
}
const handleClickDeleteForm1 = (id, title, Projekto_id) => {
    const form = document.querySelector('.pop-up__delete1');
    const deleteBtn = document.querySelector('.pop-up__confirm-btn1');
    const link = `delete1.php?Uzduoties_id=${id}&title=${title}&Projekto_id=${Projekto_id}`;
    deleteBtn.setAttribute('href', `${link}`);
    form.classList.add('pop-up__JS');
    renderBlur();
}

const handleUpdateForm = (title, description, id) => {
    const form = document.querySelector('.pop-up__update');
    const inputTitle = document.querySelector('.pop-up__update-title');
    const inputDescription = document.querySelector('.pop-up__update-description');
    const inputId = document.querySelector('.pop-up__update-id');
    inputTitle.setAttribute('value', `${title}`);
    inputId.setAttribute('value', `${id}`);
    inputDescription.innerText = `${description}`;
    form.classList.add('pop-up__JS');
    renderBlur();
}

const sendSqlQuery = task => {
    let data = new FormData();
    data.append('title', task.title);
    data.append('description', task.description);
    data.append('priority', task.priority);
    data.append('status', task.status);
    data.append('id', task.id);

    let xhr = new XMLHttpRequest();
    xhr.open('POST', "ajaxClass.php");
    xhr.onload = function() {
        // console.log(this.response);
    };
    xhr.send(data);
    return false;
}

const removeChecked = () => {
    let test = document.querySelectorAll('.pop-up__update-priority');
    let test2 = document.querySelectorAll('.pop-up__update-status');
    test.forEach(t => {
        t.removeAttribute('checked');
    });

    test2.forEach(t2 => {
        t2.removeAttribute('checked');
    });
}

const handleTaskUpdateForm = (title, priority, busena, description, id) => {
    removeChecked();
    console.log(id, description);
    const form = document.querySelector('.pop-up__update1');
    const inputTitle = document.querySelector('.pop-up__update-title1');
    const inputDescription = document.querySelector('.pop-up__update-description1');
    const inputId = document.querySelector('.pop-up__update-id1');
    const taskPriority = document.querySelector(`.priority-${priority}`);
    const masyvas = busena.split(' ');
    if (masyvas.lenght == 2) {
        masyvas.pop();
    }
    const taskBusena = document.querySelector(`.status-${masyvas[0]}`);
    inputTitle.setAttribute('value', `${title.trimStart()}`);
    inputId.setAttribute('value', `${id}`);
    inputDescription.innerText = `${description}`;
    taskPriority.setAttribute('checked', '');
    taskBusena.setAttribute('checked', '');
    form.classList.add('pop-up__JS');
    renderBlur();
}

const handleCloseForm = () => {
    const form = document.querySelector('.pop-up__JS');
    form.classList.remove('pop-up__JS');
    removeBlur();
}

const handleClickRemovePlaceholder = () => {
    const placeholder = document.querySelector('.pop-up__placeholder');
    placeholder ? placeholder.textContent = '' : '';
}

const handleClickAddPlaceholder = () => {
    const placeholder = document.querySelector('.pop-up__placeholder');
    placeholder ? placeholder.textContent = 'Description' : '';
}

const handleToggleMenu = () => {
    const body = document.body;
    body.classList.toggle('left-menu__JS');
}

const handlePrepareCsvProjectsDownload = () => {
    if (document.querySelector('[data-link]')) {
        const exportBtn = document.querySelector('.export');
        const link = document.querySelector('[data-link]').getAttribute('data-link').replace(/\n$/, '');
        exportBtn.setAttribute('href', encodeURI(link));
    }
}

const sleep = ms => {
    return new Promise((accept) => {
        setTimeout(() => {
            accept();
        }, ms);
    });
}

togglerMenu.forEach(btn => {
    btn.addEventListener('click', handleToggleMenu);
});

createProjectBtns.forEach(
    btn => btn.addEventListener('click', handleProjectCreateForm)
);

createTaskBtns.forEach(
    btn => btn.addEventListener('click', handleTaskCreateForm)
);


updateProjectBtns.forEach(
    btn => btn.addEventListener('click', () => {
        const title = btn.parentElement.parentElement.children[1].textContent;
        const description = btn.parentElement.parentElement.children[2].textContent;
        const id = btn.parentElement.children[1].id;
        handleUpdateForm(title, description, id);
    }));

updateTaskBtns.forEach(
    btn => btn.addEventListener('click', () => {
        const title = btn.parentElement.parentElement.children[1].textContent;
        const description = btn.parentElement.parentElement.children[2].textContent;
        const priority = btn.parentElement.parentElement.children[3].textContent;
        const busena = btn.parentElement.parentElement.children[4].textContent
        const id = btn.parentElement.children[1].getAttribute('data-id');
        handleTaskUpdateForm(title, priority, busena, description, id);
    }));


description.forEach(
    fullDescription => fullDescription.addEventListener('click', () => {
        const title = fullDescription.parentElement.parentElement.children[1].textContent;
        const description = fullDescription.parentElement.parentElement.children[2].textContent;
        const id = fullDescription.parentElement.parentElement.children[0].textContent;
        handleUpdateForm(title, description, id);
    }));

descriptionTasks.forEach(
    descriptionTask => descriptionTask.addEventListener('click', () => {
        const title = descriptionTask.parentElement.parentElement.children[1].textContent;
        const description = descriptionTask.parentElement.parentElement.children[2].textContent;
        const priority = descriptionTask.parentElement.parentElement.children[3].textContent;
        const busena = descriptionTask.parentElement.parentElement.children[4].textContent
        const id1 = descriptionTask.parentElement.parentElement.children[0].textContent;
        handleTaskUpdateForm(title, priority, busena, description, id1);
    }));

updateDashboard.forEach(
    item => item.addEventListener('click', () => {
        const title = item.textContent;
        const description = item.getAttribute('data-description');
        const priority = item.getAttribute('data-priority');
        const busena = item.getAttribute('data-status');
        const id1 = item.getAttribute('data-id');
        handleTaskUpdateForm(title, priority, busena, description, id1);
    }));

deleteTaskBtns.forEach(
    btn => btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const title = btn.getAttribute('data-title');
        const project_id = btn.getAttribute('data-id-project');
        handleClickDeleteForm1(id, title, project_id);
    }));

deleteProjectBtns.forEach(
    btn => btn.addEventListener('click', () => {
        const id = btn.id;
        const title = btn.parentElement.parentElement.children[1].textContent;
        handleProjectDeleteForm(id, title)
    }));

cancelBtns.forEach(
    cancelBtn => cancelBtn.addEventListener('click', handleCloseForm)
);

if (textarea) {
    textarea.addEventListener('focus', handleClickRemovePlaceholder);
    textarea.addEventListener('focusout', handleClickAddPlaceholder);
}

window.addEventListener('load', handlePrepareCsvProjectsDownload);