@props(['selector'])

<script type="text/javascript">
    document.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('{{ $selector }}')));
</script>
