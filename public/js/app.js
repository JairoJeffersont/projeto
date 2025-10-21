// =======================
// MÉTODOS JQUERY
// =======================

// Método para auto esconder alertas
$.fn.autoHideAlert = function () {
    return this.each(function () {
        const $alert = $(this);
        const timeout = parseInt($alert.attr('data-timeout'));

        if (timeout > 0) {
            setTimeout(() => {
                $alert.fadeOut(500, function () {
                    $(this).remove();
                });
            }, timeout * 1000);
        }
    });
};

// Método para popular os partidos no select
$.fn.populatePartidos = function () {
    const $selectPartido = $(this);
    const selectedPartido = $selectPartido.attr('data-selected') || '';
    const legislatura = $selectPartido.attr('data-legislatura') || '';

    // Monta a URL dinamicamente
    let url = 'https://dadosabertos.camara.leg.br/api/v2/partidos?itens=100&ordem=ASC&ordenarPor=sigla';
    if (legislatura) {
        url += `&idLegislatura=${legislatura}`;
    }

    // Estado inicial
    $selectPartido.empty().append('<option>Carregando...</option>');

    // Requisição para buscar os partidos
    $.getJSON(url, function (data) {
        const partidos = data.dados || [];
        $selectPartido.empty().append('<option value="Partido não informado">Partido não informado</option>');

        partidos.forEach(partido => {
            const isSelected = partido.sigla === selectedPartido ? 'selected' : '';
            $selectPartido.append(`<option value="${partido.sigla}" ${isSelected}>${partido.sigla}</option>`);
        });
    });
};

// Método para popular os estados no select
$.fn.populateEstados = function () {
    const $selectEstado = $(this);
    const selectedUF = $selectEstado.attr('data-selected') || '';
    $selectEstado.empty().append('<option>Carregando...</option>');

    $.getJSON('https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome', function (estados) {
        $selectEstado.empty().append('<option value="">Selecione o estado</option>');

        estados.forEach(estado => {
            const isSelected = estado.sigla === selectedUF ? 'selected' : '';
            $selectEstado.append(
                `<option value="${estado.sigla}" data-id="${estado.id}" ${isSelected}>${estado.nome}</option>`
            );
        });

        // Dispara change para popular municípios caso UF já esteja selecionada
        if (selectedUF) $selectEstado.trigger('change');
    });
};

// Método para popular os municípios baseado no estado selecionado
$.fn.populateMunicipios = function (estadoId) {
    const $selectMunicipio = $(this);
    const selectedMunicipio = $selectMunicipio.attr('data-selected') || '';
    $selectMunicipio.empty().append('<option>Carregando...</option>');

    if (!estadoId) {
        $selectMunicipio.empty().append('<option value="">Selecione o município</option>');
        return;
    }

    $.getJSON(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoId}/municipios`, function (municipios) {
        $selectMunicipio.empty().append('<option value="">Selecione o município</option>');

        municipios.forEach(municipio => {
            const isSelected = municipio.nome === selectedMunicipio ? 'selected' : '';
            $selectMunicipio.append(`<option value="${municipio.nome}" ${isSelected}>${municipio.nome}</option>`);
        });
    });
};

// Método para confirmar ação
$.fn.confirmAction = function () {
    return this.each(function () {
        $(this).on('click', function (e) {
            const message = $(this).attr('data-message') || 'Tem certeza?';

            if (!confirm(message)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        });
    });
};

// =======================
// FUNÇÕES AUXILIARES
// =======================

// Função para mostrar o modal de loading
function showLoadingModal(initialMessage = 'Aguarde, processando...', delayMessage = 'Está demorando um pouco mais que o normal...', delayTime = 10000, autoCloseTime = 30000) {
    const $modal = $('#modalLoading');
    const $message = $modal.find('.modal-body p');

    $message.text(initialMessage);
    $modal.modal('show');

    const messageTimeout = setTimeout(() => {
        $message.text(delayMessage);
    }, delayTime);

    setTimeout(() => {
        clearTimeout(messageTimeout);
        $modal.modal('hide');
    }, autoCloseTime);
}

// Função para inicializar eventos
function initEvents() {
    // Submissão de formulários que disparam o modal
    $(document).on('submit', 'form', function () {
        showLoadingModal();
    });

    // Cliques em elementos que disparam o modal
    $(document).on('click', '.loading-modal', function () {
        showLoadingModal();
    });

    // Mudança do estado para popular municípios (para qualquer formulário)
    $(document).on('change', '.estado', function () {
        const $form = $(this).closest('form');
        const estadoId = $(this).find(':selected').data('id');
        const $municipioSelect = $form.find('.municipio');
        $municipioSelect.populateMunicipios(estadoId);
    });

    // Inicializa confirmação em elementos com .confirm-action
    $('.confirm-action').confirmAction();
}

// =======================
// FUNÇÃO PARA COPIAR TEXTO
// =======================
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Link copiado para a área de transferência!');
    }).catch(err => {
        console.error('Erro ao copiar o texto: ', err);
    });
}

// =======================
// INICIALIZAÇÃO
// =======================
$(document).ready(function () {
    $('.alert[data-timeout]').autoHideAlert();

    // Popula todos os selects de estado encontrados
    $('.estado').each(function () {
        $(this).populateEstados();
    });

    // Popula todos os selects de partido (caso existam)
    $('.partidos').each(function () {
        $(this).populatePartidos();
    });

    initEvents();

    // Evento para copiar o link ao clicar
    $('#btn-copiar-link').on('click', function (e) {
        e.preventDefault();
        const link = $('#link-cadastro').text().trim();
        copyToClipboard(link);
    });
});
