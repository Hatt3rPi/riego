# import pdfplumber

# with pdfplumber.open('multisegmento-web-julio-2023.pdf') as pdf:
#     text = ''
#     for page in pdf.pages:
#         text += page.extract_text()

# print(text)


from pdf2image import convert_from_path
from PIL import Image
import pytesseract

# Convertir el PDF en una lista de imágenes
images = convert_from_path('multisegmento-web-julio-2023.pdf')

# Inicializar una variable para almacenar todo el texto
text = ''

# Aplicar OCR a cada imagen
for i, image in enumerate(images):
    text += pytesseract.image_to_string(image)

# Imprimir el texto extraído
print(text)
