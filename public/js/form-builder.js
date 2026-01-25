document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('questions-container');
  const addBtn = document.getElementById('add-question-btn');
  let questionCount = 0;

  addBtn.addEventListener('click', () => {
    addQuestion();
  });

  function addQuestion() {
    const index = questionCount++;
    const questionEl = document.createElement('div');
    questionEl.className = 'card bg-base-100 shadow p-6 mb-4 border border-base-200 question-block';
    questionEl.dataset.index = index;

    questionEl.innerHTML = `
            <input type="hidden" name="questions[${index}][position]" class="question-position" value="${document.querySelectorAll('.question-block').length}">
            <div class="flex justify-between items-start gap-4 mb-4">
                <input type="text" name="questions[${index}][text]" placeholder="Question Text" class="input input-bordered w-full" required>
                <select name="questions[${index}][type]" class="select select-bordered w-48 type-select">
                    <option value="short_answer">Short Answer</option>
                    <option value="paragraph">Paragraph</option>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="checkbox">Checkbox</option>
                </select>
                <button type="button" class="btn btn-ghost btn-circle btn-sm text-error delete-question">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="options-container space-y-2 mb-4 hidden">
                <!-- Options will be injected here -->
            </div>

            <div class="flex items-center justify-end gap-4 border-t pt-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <span class="label-text">Required</span>
                    <input type="checkbox" name="questions[${index}][required]" class="toggle toggle-primary toggle-sm">
                </label>
            </div>
        `;

    container.appendChild(questionEl);
    updatePositions();

    // Event Listeners for this question
    const typeSelect = questionEl.querySelector('.type-select');
    const optionsContainer = questionEl.querySelector('.options-container');
    const deleteBtn = questionEl.querySelector('.delete-question');

    typeSelect.addEventListener('change', () => {
      const type = typeSelect.value;
      if (type === 'multiple_choice' || type === 'checkbox') {
        optionsContainer.classList.remove('hidden');
        if (optionsContainer.querySelectorAll('.option-item').length === 0) {
          addOption(index, optionsContainer, type);
        }
        // Show add option button
        addOptionBtn.style.display = 'inline-flex';
      } else {
        optionsContainer.classList.add('hidden');
        addOptionBtn.style.display = 'none';
      }
    });

    // Add Option Button
    const addOptionBtn = document.createElement('button');
    addOptionBtn.type = 'button';
    addOptionBtn.className = 'btn btn-ghost btn-sm text-primary';
    addOptionBtn.innerText = '+ Add Option';
    addOptionBtn.style.display = 'none'; // Hidden by default
    addOptionBtn.onclick = () => addOption(index, optionsContainer, typeSelect.value);
    optionsContainer.parentNode.insertBefore(addOptionBtn, optionsContainer.nextSibling);

    deleteBtn.addEventListener('click', () => {
      questionEl.remove();
      updatePositions();
    });
  }

  function addOption(qIndex, container, type) {
    const optionDiv = document.createElement('div');
    optionDiv.className = 'flex items-center gap-2 option-item';

    const icon = type === 'multiple_choice'
      ? '<div class="w-4 h-4 rounded-full border border-base-content/50"></div>'
      : '<div class="w-4 h-4 rounded border border-base-content/50"></div>';

    optionDiv.innerHTML = `
            ${icon}
            <input type="text" name="questions[${qIndex}][options][]" placeholder="Option" class="input input-ghost input-sm w-full border-b border-base-content/20 rounded-none focus:outline-none focus:border-primary">
            <button type="button" class="btn btn-ghost btn-xs text-base-content/50 hover:text-error delete-option">✕</button>
        `;

    optionDiv.querySelector('.delete-option').addEventListener('click', () => {
      optionDiv.remove();
    });

    container.appendChild(optionDiv);
  }

  function updatePositions() {
    const questions = document.querySelectorAll('.question-block');
    questions.forEach((q, idx) => {
      const posInput = q.querySelector('.question-position');
      if (posInput) posInput.value = idx;
    });
  }

  // Add one initial question
  addQuestion();
});
