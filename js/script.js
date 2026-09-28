document.addEventListener('DOMContentLoaded', () => {
    const bancoExercicios = document.getElementById('exercicio-banco');
    const fichaDropZone = document.getElementById('ficha-drop-zone');
    const exercicioItems = document.getElementById('.exercicios-item');
    const salvarButton = document.getElementById('salvar-ficha');
    const alunoSelect = document.getElementById('aluno-select');
    const placeholderText = document.getElementById('.placeholder-text');

    let draggedItem = null;

    // --- Função de Estado e UI ---

    const updateFichaUI = () => {
        // Exibi/esconde o placeholder baseado no número de itens
        const hasItems = fichaDropZone.querySelectorAll('.treino-item').length > 0;
        placeholderText.style.display = hasItems ? 'nome' : 'block';

        //Habilita/Desabilita o botão salvar
        salvarButton.disable = !(hasItems && alunoSelect.value);
    };
    const getFichaData = () => {
        const itens = fichaDropZone.querySelectorAll('.treino-item');
        const ficha = [];
        itens.forEach(item => {
            ficha.push({
                id: item.dataset.id,
                nome: item.dataset.nome,
                series: item.querySelector('[name="series"]').value,
                repeticoes: item.querySelector('[name="repeticoes"]').value,
                carga: item.querySelector('[name="carga"]').value
            });
        });
        return ficha;
    };

    // --- Drag and Drop Logic ---

    //1. Inicio do Drag (Banco)
    exercicioItems.forEach(item => {
        item.addEventListener('dragstart', (e) => {
            draggedItem = e.target;
            e.dataTransfer.setData('text/plain', e.target.dataset.id);
            setTimeout(() => {
                e.target.classList.add('dragging');
            }, 0);
        });
        item.addEventListener('dragend', (e) => {
            e.target.classList.remove('dragging');
        });
    });
    //2. Evento da Zona de Drop (Ficha)

    fichaDropZone.addEventListener('dragover', (e) => {
        e.preventDefault(); //Necessario para permitir o Drop
        if(e.target.closest('.ficha-treino')) {
            fichaDropZone.classList.add('drag-over');
        }
    });
    fichaDropZone.addEventListener('dragleave', () => {
        fichaDropZone.classList.remove('drag-over');
    });
    fichaDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        fichaDropZone.classList.remove('drag-over');

        const id = e.dataTransfer.getData('text/plain');
        const nome = draggedItem.dataset.nome;

        if(id && nome) {
            adicionarItemNaFicha(id, nome);
        }
    });

    //3. Reordenar na Ficha (Drop dentro da Ficha)
    fichaDropZone.addEventListener('dragstart', (e) => {
        //Permite reordenar os itens que já estão na ficha
        if(e.target.classList.contains('treino-item')) {
            draggedItem = e.target;
            e.dataTransfer.setData('text/html', 'reorder'); // Diferencia do drag do banco
            setTimeout(() => e.target.classList.add('dragging'), 0);
        }
    });
    fichaDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        fichaDropZone.classList.remove('drag-over');

        if(draggedItem && draggedItem.classList.contains('treino-item')) {
            const dropTarget = e.target.closest('.treino-item');
            if(dropTarget && dropTarget != draggedItem) {
                // Lógica de reordenamento: inserre o arrastado antes ou depois do alvo
                const targetRect = dropTarget.getBoundingClienteRect();
                const mouseY = e.clientY;

                if(mouseY < targetRect.top + targetRect.height / 2) {
                    fichaDropZone.insertBefore(draggedItem, dropTarget);
                } else {
                    fichaDropZone.insertBefore(draggedItem, dropTarget.nextSibling);
                }
            }
            draggedItem.classList.remove('dragging');
        }
        updateFichaUI();
    });

    // ---Função de manipulação de Ficha ---

    const adicionarItemNaFicha = (id, nome) => {
        const item = document.createElement('div');
        item.className = 'treino-item';
        item.draggable = true;
        item.dataset.id = id;
        item.dataset.nome = nome;

        item.innerHTML = `
        <span> ${nome} </span>
        <div>
        <label>Séries:</label> <input type="number" name="series" value="3" min="1">
        <label>Reps:</label> <input type="number" name="repeticoes" value="10" min="1">
        <label>Carga (kg):</label> <input type="number" name="carga" value="0" min="0">
        <button class="remove-ex">&times;</button>
        </div> 
        `;
        fichaDropZone.appendChild(item);

        // Adicionar o elemento de Remoção
        item.querySelector('.remove-ex').addEventListener('click', (e) => {
            e.target.closest('.treino-item').remove();
            updateFichaUI();
        });

        // Adicionar o evento de dragstart/dragend para permitir reordenamento
        item.addEventListener('dragstart', (e) => {
            draggedItem = e.target;
            e.dataTransfer.setData('text/html', 'reorder');
            setTimeout(() =>e.target.classList.add('dragging'), 0);
        });
        item.addEventListener('dragend', (e) => e.target.classList.remove('dragging'));

        updateFichaUI();
    };

    // -- Eventos Finais ---

    //Habilitar/Desabilitar o botão salvar ao selecionar o aluno
    alunoSelect.addEventListener('change', updateFichaUI);

    //Salvar Fcicha (Ajax)
    salvarButton.addEventListener('click', async () => {
        const alunoId = alunoSelect.value;
        const fichaData = getFichaData();

        if(!alunoId) {
            alert('Por favor, selecione um aluno');
            return;
        }
        const formData = new FormData();
        formData.append('aluno_id', alunoId);
        formData.append('treino_json', JSON.stringify(fichaData));

        try {
            const response = await fetch('ficha.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if(result.success) {
                alert(result.message);
            } else {
                alert('Erro ao salvar: ' + result.message);
            }
        }catch (error) {
            console.error('Erro de rede: ', error);
            alert('Erro de conexão com o servidor.');
        }
    });
    // Inicia a UI
    updateFichaUI();
})