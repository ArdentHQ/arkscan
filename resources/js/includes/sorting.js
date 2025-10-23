export const sortRow = (row1, row2, row1Value, row2Value, sortAscending, sortByRowIndex) => {
    const isRow1Numeric = row1Value !== "" && !isNaN(row1Value) && row1Value !== null;
    const isRow2Numeric = row2Value !== "" && !isNaN(row2Value) && row2Value !== null;

    if (isNaN(row1Value) && isNaN(row2Value)) {
        return sortAscending
            ? row2Value.toString().localeCompare(row1Value)
            : row1Value.toString().localeCompare(row2Value);
    }

    if (isRow1Numeric && isRow2Numeric) {
        return sortAscending ? row1Value - row2Value : row2Value - row1Value;
    }

    if (isRow1Numeric && !isRow2Numeric) {
        return -1;
    }

    if (!isRow1Numeric && isRow2Numeric) {
        return 1;
    }

    if (sortByRowIndex && row1Value === row2Value) {
        return sortAscending
            ? row2.dataset["rowIndex"] - row1.dataset["rowIndex"]
            : row1.dataset["rowIndex"] - row2.dataset["rowIndex"];
    }

    return sortAscending ? 1 : -1;
};
