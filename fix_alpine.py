import os

files = [
    'perdata/umum.blade.php',
    'perdata/khusus.blade.php',
    'pidana/index.blade.php',
    'pidana/khusus.blade.php',
    'jadwal_sidang/index.blade.php',
    'kehadiran/index.blade.php',
    'berkas/index.blade.php',
    'e_berpadu/index.blade.php',
    'e_raterang/index.blade.php',
]

for f in files:
    path = os.path.join(r'c:\Aplikasi Pengadilan\resources\views', f)
    if not os.path.exists(path): continue
    with open(path, 'r', encoding='utf-8') as file:
        content = file.read()
    
    start_idx = content.find('x-data="{')
    if start_idx == -1: continue
    
    # parse until matching brace
    idx = start_idx + 8 # index of {
    brace_count = 0
    in_string = False
    quote_char = ''
    
    end_idx = -1
    for i in range(idx, len(content)):
        c = content[i]
        if not in_string:
            if c == '{': brace_count += 1
            elif c == '}': 
                brace_count -= 1
                if brace_count == 0:
                    end_idx = i
                    break
            elif c in ["'", '"', '`']:
                in_string = True
                quote_char = c
        else:
            if c == quote_char and content[i-1] != '\\':
                in_string = False
                
    if end_idx != -1:
        x_data_content = content[idx:end_idx+1]
        
        comp_name = f.split('/')[-2] + '_' + f.split('/')[-1].split('.')[0] + 'Data'
        comp_name = comp_name.replace('_', '').replace('-', '')
        
        new_content = content[:start_idx] + 'x-data="' + comp_name + '()"' + content[end_idx+2:]
        
        script_tag = f'''
<script>
    document.addEventListener('alpine:init', () => {{
        Alpine.data('{comp_name}', () => ({x_data_content}))
    }})
</script>
'''
        new_content = new_content.replace('@endsection', script_tag + '@endsection')
        
        with open(path, 'w', encoding='utf-8') as fw:
            fw.write(new_content)
        print('Updated ' + f)
