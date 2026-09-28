document.addEventListener('DOMContentLoaded', () => {
    const bancoExercicios = document.getElementById('exercicio-banco');
    const fichaDropZone = document.getElementById('ficha-drop-zone');
    const exerciciosItems = document.querySelectorAll('.exercicio-item');
    const salvarButton = document.getElementById('salvar-ficha');
    const alunoSelect = document.getElementById('aluno-select');
    const placeholderText = fichaDropZone.querySelector('.placeholder-text');

    let draggedItem = null;

    // --- Funções de Estado e UI ---

    const updateFichaUI = () => {
        // Exibe/esconde o placeholder baseado no número de itens
        const hasItems = fichaDropZone.querySelectorAll('.treino-item').length > 0;
        placeholderText.style.display = hasItems ? 'none' : 'block';
        
        // Habilita/desabilita o botão salvar
        salvarButton.disabled = !(hasItems && alunoSelect.value);
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
                carga: item.querySelector('[name="carga"]').value,
                treino: item.querySelector('[name="treino"]').value,
                obs: item.querySelector('[name="obs"]').value
            });
        });
        return ficha;
    };

    // --- Drag and Drop Logic ---

    // 1. Início do Drag (Banco)
    exerciciosItems.forEach(item => {
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

    // 2. Eventos da Zona de Drop (Ficha)
    fichaDropZone.addEventListener('dragover', (e) => {
        e.preventDefault(); // Necessário para permitir o drop
        if (e.target.closest('.ficha-treino')) {
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

        if (id && nome) {
            adicionarItemNaFicha(id, nome);
        }
    });
    
    // 3. Reordenar na Ficha (Drop dentro da Ficha)
    fichaDropZone.addEventListener('dragstart', (e) => {
        // Permite reordenar os itens que JÁ ESTÃO na ficha
        if (e.target.classList.contains('treino-item')) {
            draggedItem = e.target;
            e.dataTransfer.setData('text/html', 'reorder'); // Diferencia do drag do banco
            setTimeout(() => e.target.classList.add('dragging'), 0);
        }
    });

    fichaDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        fichaDropZone.classList.remove('drag-over');

        if (draggedItem && draggedItem.classList.contains('treino-item')) {
            const dropTarget = e.target.closest('.treino-item');
            if (dropTarget && dropTarget !== draggedItem) {
                // Lógica de reordenamento: insere o arrastado antes ou depois do alvo
                const targetRect = dropTarget.getBoundingClientRect();
                const mouseY = e.clientY;
                
                if (mouseY < targetRect.top + targetRect.height / 2) {
                    fichaDropZone.insertBefore(draggedItem, dropTarget);
                } else {
                    fichaDropZone.insertBefore(draggedItem, dropTarget.nextSibling);
                }
            }
            draggedItem.classList.remove('dragging');
        }
        updateFichaUI();
    });

    // --- Funções de Manipulação da Ficha ---
    
    const adicionarItemNaFicha = (id, nome) => {
        const item = document.createElement('div');
        item.className = 'treino-item';
        item.draggable = true;
        item.dataset.id = id;
        item.dataset.nome = nome;
        
        
        item.innerHTML = `
            <span>${nome}</span>
            <div>
                <label>Séries:</label> <input type="number" name="series" value="3" min="1">
                <label>Reps:</label> <input type="number" name="repeticoes" value="10" min="1">
                <label>Carga (kg):</label> <input type="number" name="carga" value="0" min="0">
                <label>Treino</label> <select name="treino"> <option value="a">A</option>
                <option value="b">B</option>
                <option value="c">C</option>
                <option value="d">D</option>  </select>
                <label>Obs: </label><textarea name="obs" style="width: 100%; resize: none;"></textarea>
                <button class="remove-ex">&times;</button>
            </div>
        `;
        
        fichaDropZone.appendChild(item);
        
        // Adiciona evento de remoção
        item.querySelector('.remove-ex').addEventListener('click', (e) => {
            e.target.closest('.treino-item').remove();
            updateFichaUI();
        });

        // Adiciona eventos de dragstart/dragend para permitir reordenamento
        item.addEventListener('dragstart', (e) => {
            draggedItem = e.target;
            e.dataTransfer.setData('text/html', 'reorder');
            setTimeout(() => e.target.classList.add('dragging'), 0);
        });
        item.addEventListener('dragend', (e) => e.target.classList.remove('dragging'));

        updateFichaUI();
    };


    // --- Eventos Finais ---
    
    // Habilitar/Desabilitar o botão salvar ao selecionar o aluno
    alunoSelect.addEventListener('change', updateFichaUI);

    // Salvar Ficha (AJAX)
    salvarButton.addEventListener('click', async () => {
        const alunoId = alunoSelect.value;
        const fichaData = getFichaData();
        
        if (!alunoId) {
            alert('Por favor, selecione um aluno.');
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

            if (result.success) {
                alert(result.message);
            } else {
                alert('Erro ao salvar: ' + result.message);
            }
        } catch (error) {
            console.error('Erro de rede:', error);
            alert('Erro de conexão com o servidor.');
        }
    });

    // Inicia a UI
    updateFichaUI();
});