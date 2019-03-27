import accounting from 'accounting'

export function number_comma(number)
{
    return accounting.formatNumber(number, 0)
}

export function money(number)
{
    return accounting.formatNumber(number, 2)
}