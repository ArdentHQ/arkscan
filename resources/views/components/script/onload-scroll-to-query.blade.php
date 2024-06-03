@props(['selector'])

<script type="text/javascript">
    document.addEventListener('livewire:init', () => Livewire.on('pageChanged', () => scrollToQuery('{{ $selector }}')));
</script>
