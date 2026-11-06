const POPULAR_DOMAINS = [
    'gmail.com', 'yahoo.com', 'yahoo.fr', 'hotmail.com', 'hotmail.fr', 'outlook.com', 'outlook.fr',
    'live.com', 'live.fr', 'icloud.com', 'orange.fr', 'wanadoo.fr', 'free.fr', 'laposte.net', 'sfr.fr',
    'bbox.fr', 'numericable.fr', 'gmx.com', 'gmx.fr', 'protonmail.com', 'aol.com',
];

function levenshtein(a, b) {
    const rows = a.length + 1;
    const cols = b.length + 1;
    const d = Array.from({ length: rows }, () => new Array(cols).fill(0));

    for (let i = 0; i < rows; i++) d[i][0] = i;
    for (let j = 0; j < cols; j++) d[0][j] = j;

    for (let i = 1; i < rows; i++) {
        for (let j = 1; j < cols; j++) {
            d[i][j] = a[i - 1] === b[j - 1]
                ? d[i - 1][j - 1]
                : 1 + Math.min(d[i - 1][j], d[i][j - 1], d[i - 1][j - 1]);
        }
    }

    return d[rows - 1][cols - 1];
}

// flat popular-domain list + plain Levenshtein, not a real spellchecker — good enough for "gamil"/"gmail.ocm" style typos
function suggestDomain(domain) {
    let best = null;
    let bestDistance = Infinity;

    for (const candidate of POPULAR_DOMAINS) {
        if (candidate === domain) {
            return null;
        }

        const distance = levenshtein(domain, candidate);
        if (distance < bestDistance) {
            bestDistance = distance;
            best = candidate;
        }
    }

    const threshold = domain.length > 6 ? 2 : 1;

    return best !== null && bestDistance > 0 && bestDistance <= threshold ? best : null;
}

// "jean@gmial.com" => "jean@gmail.com", null when nothing to suggest
export default function suggestEmail(value) {
    const at = (value || '').lastIndexOf('@');
    if (at === -1 || at === value.length - 1) {
        return null;
    }

    const fixedDomain = suggestDomain(value.slice(at + 1).toLowerCase());
    return fixedDomain ? value.slice(0, at + 1) + fixedDomain : null;
}
