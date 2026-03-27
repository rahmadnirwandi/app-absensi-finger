var base_url = $(document).find('#base_url').val();


Inputmask.extendAliases({
    money: {
        alias: 'decimal',
        groupSeparator: '.',
        radixPoint: ',',
        autoGroup: true,
        removeMaskOnSubmit: true,
        unmaskAsNumber: true
    }
});

const objectsEqual = (o1, o2) =>
    typeof o1 === 'object' && Object.keys(o1).length > 0
        ? Object.keys(o1).length === Object.keys(o2).length
        && Object.keys(o1).every(p => objectsEqual(o1[p], o2[p]))
        : o1 === o2;

const arraysEqual = (a1, a2) =>
    a1.length === a2.length && a1.every((o, idx) => objectsEqual(o, a2[idx]));
// $(document).find(".money").inputmask({ alias : "money" });