const fs = require('fs');

export function createFolderIfNotExists(folder: string): void { 
    if (!fs.existsSync(folder)) {
        fs.mkdirSync(folder, { recursive: true });
    }
}
