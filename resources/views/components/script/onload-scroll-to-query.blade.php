@props(['selector'])

<script type="text/javascript">
    document.addEventListener('livewire:load', () => Livewire.on('pageChanged', () => scrollToQuery('{{ $selector }}')));
</script>
